//
//  MonitorServer.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/30/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "AddRequest.h"

void AddRequest::processRequest(string &s)
{
	if (fork() == 0)
	{
		sqlite3 *db;
		sqlite3_open(Configuration::ProblemSchemaDB.c_str(),&db);
		int version;
		Problem p;
		istringstream sin(s);
		sin>>version;
		sin>>p.id>>p.type>>p.compare>>p.input>>p.output;
		p.addSchema(db);
		int ncase;
		sin>>ncase;
		for (int i=0;i<ncase;i++)
		{
			TestCase c;
			c.problemID = p.id
			sin>>c.caseID>>c.input>>c.answer>>c.timeLimit>>c.memoryLimit>>c.score;
			c.addSchema(db);
		}
		int ndep;
		sin>>ndep;
		for (int i=0;i<ndep;i++)
		{
			Dependency dep;
			sin>>dep.filename>>dep.type;
			dep.addSchema(db,p.id);
		}
		sqlite3_close(db);
		// Process files
		// Deprecated. use ftp instead
		/*
		int nfiles;
		sin>>nfiles;
		for (int i=0;i<nfiles;i++)
		{
			ostringstream dir;
			string filename;
			sin>>filename;
			string file;
			sin>>file;
			dir<<Configuration::DataDirPrefix<<p.id<<'/'<<filename;
			writeBase64(dir.str(),file);
		}
		*/
		string format,filename;
		sin>>format>>filename;
		string path = Configuration::DataDirPrefix + filename;
		ostringstream outputdir(Configuration::DataDirPrefix);
		outputdir<<p.id<<'/';
		if (fork()==0)
		{
			execl("unzip","unzip","-jqq",path.c_str(),"-d",outputdir.str().c_str(),NULL);
		}
		else
		{
			wait(NULL);
			unlink(path.c_str());
		}
	}

}
