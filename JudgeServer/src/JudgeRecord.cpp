//
//  JudgeRecord.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/15/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "JudgeRecord.h"

void JudgeRecord::parse(xml_node<> *request)
{
	// TODO Error Handling
	xml_node<> *problemNode = request->first_node("problem");
	sscanf(problemNode->first_attribute("id")->value(),"%d",&problemID);
	
	xml_node<> *recordNode = request->first_node("record");
	sscanf(recordNode->first_attribute("id")->value(),"%d",&recordID);
	
	xml_node<> *submissionNode = request->first_node("submission");
	xml_attribute<> *languageAttribute = submissionNode->first_attribute("lang");
	if (languageAttribute)
	{
		language = string(languageAttribute->value());
	}
	
	ConfigFile *config = Configuration::Get();
		
	ostringstream sout1;
	sout1<<config->read<string>("data_dir_prefix")<<problemID<<'/';
	dataDirectory = sout1.str();

	ostringstream sout;
	sout<<config->read<string>("working_dir_prefix")<<recordID<<'/';
	workingDirectory = sout.str();
	
	sout<<recordID;
	binaryPath = sout.str();
	sout<<'.'<<language;
	submissionPath = sout.str();

	mkdir(workingDirectory.c_str(), 0777);
	
	xml_attribute<> *pathAttribute = submissionNode->first_attribute("path");
	
	if (pathAttribute)
	{
		rename(pathAttribute->value(), submissionPath.c_str());
	}
	else
	{
		writeBase64(submissionPath.c_str(),submissionNode->value(),submissionNode->value_size());
	}
	
}


void JudgeRecord::compile()
{
	// Prepare dependencies
	for (vector<Dependency>::iterator it = dependencies.begin();it != dependencies.end();it++)
	{
		char source[512];
		char dest[512];
		strcpy(source,dataDirectory.c_str());
		strcat(source,(*it).filename.c_str());

		strcpy(dest,workingDirectory.c_str());
		strcat(dest,(*it).filename.c_str());

		cp(source,dest);
	}

	if (language.compare("cpp") == 0)
	{
		compiler = dynamic_cast<Compiler*>(new Compiler_CPP);
	}
	else if (language.compare("c") == 0)
	{
		compiler = dynamic_cast<Compiler*>(new Compiler_C);
	}
	else if (language.compare("pas") == 0)
	{
		compiler = dynamic_cast<Compiler*>(new Compiler_PAS);
	}else
	{
		syslog(LOG_INFO, "unknown language");
		compiler = NULL;
		
		return;
	}

	// TODO compiler time limit
	compiler->compile(submissionPath, binaryPath, -1);
	syslog(LOG_INFO, "compiled");
}

void JudgeRecord::judge()
{
	loadProblemSchema();
	compile();
	if (compiler && compiler->success)
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
	
	if (fork() == 0)
	{
		execl("/bin/rm","rm","-rf",workingDirectory.c_str(),NULL);
	}
}

void JudgeRecord::loadProblemSchema()
{
	sqlite3 *schemaDB;
	sqlite3_open(Configuration::Get()->read<string>("problem_schema_db").c_str(), &schemaDB);
	sqlite3_stmt *stmt;
	char query[] = "SELECT type,compare,input,output FROM problems WHERE `id` = ?";
	sqlite3_prepare_v2(schemaDB, query, sizeof(query), &stmt, NULL);
	sqlite3_bind_int(stmt, 1, problemID);
	if (sqlite3_step(stmt) == SQLITE_ROW)
	{
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
		char caseQuery[] = "SELECT cid,input,answer,time_limit,memory_limit,score FROM testcases WHERE pid = ?";
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

	}
	else
	{
		cerr<<"Unable to fetch schema. "<<endl;
	}
	sqlite3_close(schemaDB);
}
