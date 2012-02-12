//
//  RunScheduler.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 9/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "RunScheduler.h"

RunScheduler::RunScheduler(int _nCPU, int _concurrency, int _waitlistCapacity)
{
	nCPU = _nCPU;
	concurrency = _concurrency;
	waitlistCapacity = _waitlistCapacity;
		
	cpus = new int[nCPU];
		
	runningJobs = 0;
		
}

int RunScheduler::arrangeTask(JudgeRecord* record)
{
	JudgeRecord *currentRecord;
	if (runningJobs >= concurrency)
	{
		if (waitlist.size() >= waitlistCapacity)
		{
			return SERVERCODE_FULL;
		}
		waitlist.push(record);
		return SERVERCODE_WAITLISTED;
	}
	else
	{
		if (waitlist.empty())currentRecord = record;
		else
		{
			currentRecord = waitlist.front();
			waitlist.pop();
			waitlist.push(record);
		}
		runTask(currentRecord);
	}
	return SERVERCODE_SUCCESS;
}

void RunScheduler::runTask(JudgeRecord *record)
{
	runningJobs++;
	int minSize = 1<<30;
	short mini;
	for (int i=0;i<nCPU;i++)
	{
		if (cpus[i] < minSize)
		{
			minSize = cpus[i];
			mini = i;
		}
	}
	cpus[mini]++;

	//record->cpu = mini;

	pid_t pid = fork();
	if (pid == 0)
	{
		record->judge();
		WebServer server;
		server.pushResult(record);
		struct msgEval msg;
		msg.mtype = 1;
		msg.cpuid = mini;
		msgsnd(msgQueue,&msg,sizeof(msgEval),0);
		delete record;

		exit(0);
	}
	else
	{
		cpu_set_t cpumask;

		CPU_ZERO(&cpumask);
		CPU_SET(mini,&cpumask);

		sched_setaffinity(pid,sizeof(cpu_set_t),&cpumask);
		delete record;
	}
}

void RunScheduler::removeTask(int cpuid)
{
	runningJobs--;
	cpus[cpuid]--;
	if (!waitlist.empty())
	{
		JudgeRecord *record = waitlist.front();
		waitlist.pop();
		runTask(record);
	}
}

bool RunScheduler::serverBusy()
{
	return waitlist.size() >= waitlistCapacity && runningJobs >= concurrency;
}

int RunScheduler::serverWorkload()
{
	return waitlist.size() + runningJobs;
}
