//
//  MonitorServer.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/30/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "AddRequest.h"

void AddRequest::parse(xml_node<> *request)
{
	xml_node<> *problemNode = request->first_node("problem");
	xml_node<> *archiveNode = request->first_node("archive");
	xml_node<> *caseListNode = request->first_node("cases");
	
	if (!problemNode || !archiveNode || !caseListNode)
	{
		syslog(LOG_ERR, "Invalid add request");
		return;
	}
	
	ConfigFile* config = Configuration::Get();
	sqlite3 *db;
	int code = sqlite3_open(config->read<string>("problem_schema_db").c_str(),&db);
	if (code)
	{
		syslog(LOG_ERR,"Open DB Error: %d",code);
	}
	
	Problem p;
	
	sscanf(problemNode->first_attribute("id")->value(),"%d",&p.id);
	sscanf(problemNode->first_attribute("type")->value(),"%d",&p.type);
	p.compare = problemNode->first_attribute("compare")->value();
	p.input = problemNode->first_attribute("input")->value();
	p.output = problemNode->first_attribute("output")->value();
	p.addSchema(db);
	
	xml_node<> *curCase  = caseListNode->first_node("case");
	
	while (curCase)
	{
		TestCase c;
		c.problemID = p.id;
		sscanf(curCase->first_attribute("id")->value(),"%d",&c.caseID);
		c.input = curCase->first_attribute("input")->value();
		c.answer = curCase->first_attribute("answer")->value();
		sscanf(curCase->first_attribute("timelimit")->value(),"%lf",&c.timeLimit);
		sscanf(curCase->first_attribute("memorylimit")->value(),"%d",&c.memoryLimit);
		sscanf(curCase->first_attribute("score")->value(),"%d",&c.score);
		c.addSchema(db);

		curCase = curCase->next_sibling();
	}
	
	// TODO dependency
	
	sqlite3_close(db);
	
	
	ostringstream path;
	path<<config->read<string>("temp_dir_prefix")<<archiveNode->first_attribute("filename")->value();
	ostringstream outputdir;
	outputdir<<config->read<string>("data_dir_prefix")<<p.id<<'/';

	pid_t cld = fork();
	if (cld == 0)
	{
		execl("/usr/bin/unzip","unzip","-jqqo",path.str().c_str(),"-d",outputdir.str().c_str(),NULL);
	}else
	{
		waitpid(cld,NULL,0);
		unlink(path.str().c_str());
	}
}
