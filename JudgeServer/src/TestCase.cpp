//
//  TestCase.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/25/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//
#include "TestCase.h"

bool iswhite(char c)
{
	return c==' ' || c==EOF || c=='\n';
}

bool isenter(char c)
{
	return c=='\n' || c==EOF;
}

void TestCase::run()
{
	initCallAllowance();
	// Prepare test data
	char inputDataOldPath[256];
	strcpy(inputDataOldPath, record->dataDirectory.c_str());
	strcat(inputDataOldPath, input.c_str());

	strcpy(inputDataPath, record->workingDirectory.c_str());
	if (record->input.compare("/SCREEN/") == 0)
	{
		strcat(inputDataPath, "in.txt");
	}
	else
	{
		strcat(inputDataPath, record->input.c_str());
	}

	strcpy(outputDataPath, record->workingDirectory.c_str());
	if (record->output.compare("/SCREEN/") == 0)
	{
		strcat(outputDataPath, "out.txt");
	}else
	{
		strcat(outputDataPath, record->output.c_str());
	}

	cp(inputDataOldPath,inputDataPath);

	// Prepare pipe for IPC
	int pd[2];
	pipe(pd);
	
	pid_t cld = fork();
	if (cld == 0)
	{
		// in case of screen input / output, redirect stdio
		if (record->input.compare("/SCREEN/") == 0)
		{
			freopen(inputDataOldPath,"r",stdin);
		}

		if (record->output.compare("/SCREEN/") == 0)
		{
			freopen(outputDataPath,"w",stdout);
		}
		chdir(record->workingDirectory.c_str());

		if (getuid() == 0)
		{
			chroot(record->workingDirectory.c_str());
			// relinquish root privilege
			setuid(Configuration::AgentUID);
		}

		char binaryName[128];
		sprintf(binaryName, "%d", record->recordID);
		double convertedTimeLimit = timeLimit * Configuration::TimeMultipler;
		pid_t pid = fork();
		if (pid == 0)
		{
			struct rlimit rl_time_limit,rl_output_limit;

			//Set CPU Time Limit
			rl_time_limit.rlim_cur = int(ceil(convertedTimeLimit));
			setrlimit(RLIMIT_CPU,&rl_time_limit);

			//Set Output File Size Limit
			rl_output_limit.rlim_max = rl_output_limit.rlim_cur = OUTPUT_LIMIT*1024*1024;
			setrlimit(RLIMIT_FSIZE,&rl_output_limit);

			ptrace(PT_TRACE_ME, NULL, NULL, NULL);

			execl(binaryName, binaryName, NULL);
		}
		else
		{
			int resultCode = 0;
			int resultExtendedCode = 0;
			double timeUsed = 0.0;
			int memoryUsed = 0;

			struct timeval time_passed;
			struct rusage rinfo;
			int runstat = 0;
			int mem_cur;

			for (;;)
			{

				// Suspend the child process to check its state
				wait4(pid,&runstat,0,&rinfo);

				//Get current memory usage and time
				mem_cur = getpagesize()*rinfo.ru_minflt;

				time_passed = rinfo.ru_utime;

				timeUsed = (double)time_passed.tv_sec + (double)time_passed.tv_usec / 1000000.0;

				// Record the maximum of memory usage
				if (mem_cur > memoryUsed)
					memoryUsed = mem_cur;

				// Time limit exceed
				if (timeUsed > convertedTimeLimit)
				{
					ptrace(PT_KILL,pid,NULL,NULL);
					resultCode = TESTRESULT_TLE;
					break;
				}

				// Memory limit exceed
				if (memoryUsed > (memoryLimit*1024*1024))
				{
					ptrace(PT_KILL,pid,NULL,NULL);
					resultCode = TESTRESULT_MLE;
					break;
				}

				//Program exited normally
				if (WIFEXITED(runstat))
				{
					resultCode = TESTRESULT_INDETERMINE;
					break;
				}

				//Program exited abnormally
				else if (WIFSIGNALED(runstat) || WIFSTOPPED(runstat))
				{
					int abrmsg;
					if (WIFSIGNALED(runstat))
						abrmsg = WTERMSIG(runstat);
					else
						abrmsg = WSTOPSIG(runstat);

					if (abrmsg != SIGTRAP)
					{
						ptrace(PT_KILL,pid,NULL,NULL);

						// Time limit exceed caused by CPU
						if (abrmsg == SIGXCPU)
						{
							resultCode = TESTRESULT_TLE;
						}
						// Output limit exceed
						else if (abrmsg == SIGXFSZ)
						{
							resultCode = TESTRESULT_OLE;
						}
						// Runtime error
						else
						{
							resultCode = TESTRESULT_RTE;
							resultExtendedCode = abrmsg;
						}
						break;
					}
				}

				struct user_regs_struct reg;

				int syscall;

				ptrace(PTRACE_GETREGS,pid,NULL,&reg);

#ifdef __i386__
				syscall = reg.orig_eax;
#else
				syscall = reg.orig_rax;
#endif

				if (syscall)
				{
					if (callAllowance[syscall])
					{
						callAllowance[syscall]--;
					}
					else
					{
						// Forbidden system call raised by user program
						ptrace(PTRACE_KILL,pid,NULL,NULL);

						resultCode = TESTRESULT_SYSCALL;
						resultExtendedCode = syscall;
						break;
					}
				}

				// Continue executing child process
				ptrace(PTRACE_SYSCALL,pid,NULL,NULL);
			}

			// Send result back to father process
			// Child. open pipe for writing
			close(pd[0]);
			FILE *pout = fdopen(pd[1],"w");
			fwrite(&resultCode, sizeof(resultCode),1,pout);
			fwrite(&resultExtendedCode, sizeof(resultExtendedCode),1,pout);
			fwrite(&timeUsed, sizeof(timeUsed),1,pout);
			fwrite(&memoryUsed, sizeof(memoryUsed),1,pout);
			fclose(pout);

			exit(0);
		}

	}
	else
	{
		close(pd[1]);
		FILE *pin = fdopen(pd[0],"r");
		fread(&result, sizeof(result),1,pin);
		fread(&resultExtended, sizeof(resultExtended),1,pin);
		fread(&actualTime, sizeof(actualTime),1,pin);
		fread(&bytesActualMemory, sizeof(bytesActualMemory),1,pin);
		fclose(pin);
		waitpid(cld,NULL,0);
	}
}

void TestCase::compare()
{
	// no comp needed
	if (result == TESTRESULT_INDETERMINE)
	{
		char answerPath[256];
		strcpy(answerPath, record->dataDirectory.c_str());
		strcat(answerPath, answer.c_str());
		bool fulltext = record->compare.compare(COMPARE_FULLTEXT) == 0;

		if (fulltext || record->compare.compare(COMPARE_OMITSPACE) == 0)
		{
			FILE *userOutput = fopen(outputDataPath,"r");
			
			if (!userOutput)
			{
				score = 0;
				result = TESTRESULT_WA;
				return;
			}
			
			FILE *answerData = fopen(answerPath,"r");
			for (;;)
			{
				char ca;
				do
				{
					ca = fgetc(userOutput);
				}while (ca == '\r');

				char cb;
				do
				{
					cb = fgetc(answerData);
				}while (cb == '\r');


				if (ca != cb)
				{
					if (fulltext)
					{
						result = TESTRESULT_WA;
						score = 0;
						break;
					}
					else
					{
						if (!(iswhite(ca)&&iswhite(cb)))
						{
							result = TESTRESULT_WA;
							score = 0;
							break;
						}
						while (ca == ' ')
						{
							ca = fgetc(userOutput);
						}
						while (cb == ' ')
						{
							ca = fgetc(userOutput);
						}

						if (!(isenter(ca) && isenter(cb)))
						{
							result = TESTRESULT_WA;
							score = 0;
							break;
						}
					}

				}
				if (feof(userOutput) && feof(answerData))
				{
					result = TESTRESULT_OK;
					break;
				}
			}
			fclose(userOutput);
			fclose(answerData);
		}
		else
		{
			// Prepare special judge path
			char specialJudge[512];
			strcpy(specialJudge,Configuration::DataDirPrefix.c_str());
			strcat(specialJudge, record->compare.c_str());
			char specialJudgeBin[128];
			strcpy(specialJudgeBin, record->compare.c_str());
			char scorePath[512];
			strcpy(scorePath,record->workingDirectory.c_str());
			strcat(scorePath, "score.log");
			unlink(scorePath);

			char scorestr[4];
			sprintf(scorestr,"%d",score);
			pid_t cld = vfork();
			if (cld == 0)
			{
				// Run file
				execl(specialJudge,specialJudgeBin,scorestr,answerPath,NULL);
			}
			else
			{
				waitpid(cld,NULL,0);

				// Load score
				FILE *scoreFile = fopen(scorePath, "r");
				fscanf(scoreFile,"%d",&score);
				fclose(scoreFile);

				// TODO Log feature
			}
		}

	}
	else
	{
		score = 0;
	}
}

void TestCase::cleanup()
{
	unlink(inputDataPath);
	unlink(outputDataPath);
}

void TestCase::initCallAllowance()
{
	memset(callAllowance,20,sizeof(callAllowance));
	callAllowance[SYS_setuid] =
	callAllowance[SYS_getuid] =
	callAllowance[SYS_geteuid] =
	callAllowance[SYS_setreuid] =
	callAllowance[SYS_setfsuid] =
	callAllowance[SYS_setresuid] =
	callAllowance[SYS_getresuid] =
	callAllowance[SYS_getuid32] =
	callAllowance[SYS_geteuid32] =
	callAllowance[SYS_setreuid32] =
	callAllowance[SYS_setresuid32] =
	callAllowance[SYS_getresuid32] =
	callAllowance[SYS_setuid32] =
	callAllowance[SYS_setfsuid32] =

	callAllowance[SYS_setgid] =
	callAllowance[SYS_getgid] =
	callAllowance[SYS_getegid] =
	callAllowance[SYS_setpgid] =
	callAllowance[SYS_setregid] =
	callAllowance[SYS_getpgid] =
	callAllowance[SYS_setfsgid] =
	callAllowance[SYS_setresgid] =
	callAllowance[SYS_getresgid] =
	callAllowance[SYS_getgid32] =
	callAllowance[SYS_getegid32] =
	callAllowance[SYS_setregid32] =
	callAllowance[SYS_setresgid32] =
	callAllowance[SYS_getresgid32] =
	callAllowance[SYS_setgid32] =
	callAllowance[SYS_setfsgid32] =

	callAllowance[SYS_setgroups] =


	callAllowance[SYS_unlink] =
	callAllowance[SYS_mkdir] =
	callAllowance[SYS_rmdir] =
	callAllowance[SYS_rename] =
	callAllowance[SYS_chdir] =
	callAllowance[SYS_chmod] =
	callAllowance[SYS_chown] =
	callAllowance[SYS_chroot] =
	callAllowance[SYS_mount] =
	callAllowance[SYS_umount] =
	callAllowance[SYS_umount2] =
	callAllowance[SYS_mknod] =
	callAllowance[SYS_select] =
	callAllowance[SYS_creat] =

	callAllowance[SYS_fork] =
	callAllowance[SYS_clone] =
	callAllowance[SYS_ptrace] =
	callAllowance[SYS_vfork] =
	callAllowance[SYS_wait4] =
	callAllowance[SYS_getpid] =
	callAllowance[SYS_getpgrp] =
	callAllowance[SYS_kill] =
	callAllowance[SYS_vhangup] =
	callAllowance[SYS_vserver] =
	callAllowance[SYS_tkill] =
	callAllowance[SYS_pause] =
	0;

	callAllowance[SYS__sysctl]=0;
	//callAllowance[SYS_access]=0;
	//callAllowance[SYS_close]=0;

	callAllowance[SYS_create_module]=0;
	callAllowance[SYS_delete_module]=0;


	//callAllowance[SYS_open]=0;

	callAllowance[SYS_reboot]=0;

	callAllowance[SYS_restart_syscall]=0;

	callAllowance[SYS_setitimer]=0;

	callAllowance[SYS_sethostname]=0;
	callAllowance[SYS_setrlimit]=0;

	callAllowance[SYS_settimeofday]=0;

	callAllowance[SYS_clock_nanosleep]=0;
	callAllowance[SYS_nanosleep]=0;
	//callAllowance[SYS_arch_prctl]=0;
	//callAllowance[SYS_write]=0;
	//callAllowance[SYS_writev]=0;

	#ifndef __i386__
	callAllowance[SYS_accept]=0;
	callAllowance[SYS_bind]=0;
	callAllowance[SYS_connect]=0;
	callAllowance[SYS_listen]=0;
	callAllowance[SYS_socket]=0;
	#else
	callAllowance[SYS_signal]=0;
	callAllowance[SYS_waitpid]=0;
	callAllowance[SYS_nice]=0;
	callAllowance[SYS_waitpid]=0;
	#endif
	callAllowance[SYS_execve] = 1;
}

void TestCase::addSchema(sqlite3 *db)
{
	char query[] = "INSERT INTO `testcases` (pid,cid,input,answer,time_limit,memory_limit,score) VALUES (?,?,?,?,?,?,?)";
	sqlite3_stmt *stmt;
	sqlite3_prepare_v2(db,query,sizeof(query),&stmt,NULL);
	sqlite3_bind_int(stmt,1,problemID);
	sqlite3_bind_int(stmt,2,caseID);
	sqlite3_bind_text(stmt,3,input.c_str(),input.size(),NULL);
	sqlite3_bind_text(stmt,4,answer.c_str(),answer.size(),NULL);
	sqlite3_bind_double(stmt,5,timeLimit);
	sqlite3_bind_double(stmt,6,memoryLimit);
	sqlite3_bind_int(stmt,7,score);
	int code = sqlite3_step(stmt);
	if (code != SQLITE_OK && code != SQLITE_DONE)
	{
		syslog(LOG_ERR,"Add Case Error: %d\n",code);
	}
	sqlite3_finalize(stmt);
}
