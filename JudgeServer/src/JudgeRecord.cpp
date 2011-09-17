//
//  JudgeRecord.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/15/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "JudgeRecord.h"

inline char JudgeRecord::base64chr(char c)
{
    if (c == '+')return 62;
    if (c == '/')return 63;
    if (c >= 'A' && c <= 'Z')return c-'A';
    if (c >= 'a' && c <= 'z')return 26+c-'a';
    if (c >= '0' && c <= '9')return 52+c-'0';
    return -1;
}

void JudgeRecord::prepareProblem(string s)
{
    istringstream sin(s);
    string line;
    while (getline(sin,line))
    {
        if (line.size() == 0)continue;
        istringstream lin(line);
        string op;
        lin>>op>>ws;
        if (op.compare("ProblemID") == 0)
        {
            lin>>problemID;

            continue;
        }
        if (op.compare("RecordID") == 0)
        {
            lin>>recordID;

            continue;
        }
        string param;
        getline(lin, param);
        if (op.compare("Lang") == 0)
        {
            language = param;
            continue;
        }
        if (op.compare("FilePath") == 0)
        {
            deduceVariable();
            
            rename(param.c_str(), submissionPath.c_str());
            
            continue;
        }
        if (op.compare("Submission") == 0)
        {
        	deduceVariable();

            FILE *out = fopen(submissionPath.c_str(), "w");
            for (unsigned int i=0;i<param.size();i+=4)
            {
                putc((base64chr(param.at(i))<<2)+((base64chr(param.at(i+1))&0x30)>>4), out);
                if (base64chr(param.at(i+2)) >= 0)
                    putc(((base64chr(param.at(i+1))&0x0F)<<4)+((base64chr(param.at(i+2))&0x3C)>>2), out);
                if (base64chr(param.at(i+3)) >= 0)
                    putc(((base64chr(param.at(i+2))&0x03)<<6)+base64chr(param.at(i+3)), out);
            }
            fclose(out);
        }
    }
}

void JudgeRecord::deduceVariable()
{
	if (!deducedVariable)
	{
		deducedVariable = true;
		ostringstream sout1;
		sout1<<Configuration::DataDirPrefix<<problemID<<'/';
		dataDirectory = sout1.str();

		// Prepare for proper path
		ostringstream sout;
		sout<<Configuration::WorkingDirPrefix<<recordID<<'/';
		workingDirectory = sout.str();
		sout<<recordID;
		binaryPath = sout.str();
		sout<<'.'<<language;
		submissionPath = sout.str();

		mkdir(workingDirectory.c_str(), 0755);

	}
}

void JudgeRecord::compile()
{
    if (language.compare("cpp") == 0)
    {
        compiler = dynamic_cast<Compiler*>(new Compiler_CPP);
    }
    else if (language.compare("c") == 0)
    {
        compiler = dynamic_cast<Compiler*>(new Compiler_C);
    }else
    {
        throw 1;
    }

    // TODO compiler time limit
    compiler->compile(submissionPath, binaryPath, -1);

}

void JudgeRecord::judge()
{
	loadProblemSchema();
    compile();
    if (compiler->success)
    {
    	for (vector<TestCase>::iterator it=cases.begin(); it != cases.end(); it++)
    	{
    		(*it).run();
    		(*it).compare();
    		(*it).cleanup();
    	}
    }
    else
    {
    	status = RECORDSTATUS_CE;
    }
    char working[512];
    strcpy(working,workingDirectory.c_str());
    if (vfork() == 0)
    {
    	execl("/bin/rm","rm","-rf",working,NULL);
    }
}

void JudgeRecord::loadProblemSchema()
{
    sqlite3 *schemaDB;
    sqlite3_open(Configuration::ProblemSchemaDB.c_str(), &schemaDB);
    sqlite3_stmt *stmt;
    char query[] = "SELECT type,compare,input,output FROM problems WHERE id = ?";
    sqlite3_prepare_v2(schemaDB, query, sizeof(query), &stmt, NULL);
    sqlite3_bind_int(stmt, 1, problemID);
    sqlite3_step(stmt);
    type = sqlite3_column_int(stmt, 0);
    compare = string((const char*)sqlite3_column_text(stmt, 1));
    input = string((const char*)sqlite3_column_text(stmt, 2));
    output = string((const char*)sqlite3_column_text(stmt, 3));
	sqlite3_finalize(stmt);
    
    // Fetch dependencies
    sqlite3_stmt *depStmt;
    char depQuery[] = "SELECT filename,type FROM dependencies WHERE pid = ?";
    sqlite3_prepare_v2(schemaDB, depQuery, sizeof(depQuery), &depStmt, NULL);
    sqlite3_bind_int(depStmt, 1, problemID);
    
    while (sqlite3_step(depStmt) == SQLITE_ROW)
    {
        Dependency dep;
        dep.filename = string((const char*)sqlite3_column_text(depStmt, 0));
        dep.type = sqlite3_column_int(depStmt, 1);
        dependencies.push_back(dep);
    }
    
    sqlite3_finalize(depStmt);
    
    sqlite3_stmt *caseStmt;
    char caseQuery[] = "SELECT cid,input,output,time_limit,memory_limit,score FROM testcases WHERE pid = ?";
    sqlite3_prepare_v2(schemaDB, caseQuery, sizeof(caseQuery), &caseStmt, NULL);
    sqlite3_bind_int(caseStmt, 1, problemID);
    while (sqlite3_step(caseStmt) == SQLITE_ROW)
    {
        TestCase curCase;
        curCase.record = this;
        curCase.caseID = sqlite3_column_int(caseStmt, 0);
        curCase.input = string((const char*)sqlite3_column_text(caseStmt, 1));
        curCase.answer = string((const char*)sqlite3_column_text(caseStmt, 2));
        curCase.timeLimit = sqlite3_column_double(caseStmt, 3);
        curCase.memoryLimit = sqlite3_column_int(caseStmt, 4);
        curCase.score = sqlite3_column_int(caseStmt, 5);
        cases.push_back(curCase);
    }
    
    sqlite3_finalize(caseStmt);
    sqlite3_close(schemaDB);
}
