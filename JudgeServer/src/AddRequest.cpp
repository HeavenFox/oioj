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
			sin>>c.problemID>>c.caseID>>c.input>>c.answer>>c.timeLimit>>c.memoryLimit;
			c.addSchema(db);
		}
		int ndep;
		sin>>ndep;
		for (int i=0;i<ndep;i++)
		{

		}
		sqlite3_close(db);
		// Process files
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
		exit(0);
	}

}
