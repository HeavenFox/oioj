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
	//if (fork() == 0)
	//{
		sqlite3 *db;
		int code = sqlite3_open(Configuration::ProblemSchemaDB.c_str(),&db);
		if (code)
		{
			syslog(LOG_ERR,"Open DB Error: %d",code);
		}
		int version;
		Problem p;
		istringstream sin(s);
		sin>>version;
		sin>>p.id>>p.type>>p.compare>>p.input>>p.output;
		syslog(LOG_INFO,"Adding Problem %d",p.id);
		p.addSchema(db);
		int ncase;
		sin>>ncase;
		for (int i=0;i<ncase;i++)
		{
			TestCase c;
			c.problemID = p.id;
			sin>>c.caseID>>c.input>>c.answer>>c.timeLimit>>c.memoryLimit>>c.score;
			c.addSchema(db);
		}
		syslog(LOG_INFO,"%d cases added",ncase);
		int ndep;
		sin>>ndep;
		for (int i=0;i<ndep;i++)
		{
			Dependency dep;
			sin>>dep.filename>>dep.type;
			dep.addSchema(db,p.id);
		}
		syslog(LOG_INFO,"%d dependencies added",ndep);
		
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
		string format;
		sin>>format;
                ostringstream path;
                path<<Configuration::TempDirPrefix<<p.id<<"."<<format;
		ostringstream outputdir;
		outputdir<<Configuration::DataDirPrefix<<p.id<<'/';
                
		if (fork() == 0)
		{
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

}
