//
//  Configuration.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 9/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "Configuration.h"

int Configuration::PortNumber;
int Configuration::AgentUID;
int Configuration::ConcurrentJobs;
int Configuration::CPUCount;
double Configuration::CompilerTimeLimit;
double Configuration::TimeMultipler;
int Configuration::WaitlistSize;
string Configuration::WorkingDirPrefix;
string Configuration::DataDirPrefix;
string Configuration::TempDirPrefix;
string Configuration::ProblemSchemaDB;
string Configuration::Token;
string Configuration::WebServer;
string Configuration::WebServerCallbackScript;

void Configuration::ReadConfiguration()
{
    ifstream fin("/etc/oioj/oioj.conf");
    
    string s;
    
    while (getline(fin,s))
    {
        if (s.size() > 1 && s.at(0) != '#'){
        	trim(s);

            istringstream sin(s);
            string op,param;
            sin>>op>>ws;
            
            if (op.compare("PortNumber") == 0)
            {
                sin>>PortNumber;
                continue;
            }
            
            if (op.compare("AgentUID") == 0)
            {
                sin>>AgentUID;
                continue;
            }
            if (op.compare("ConcurrentJobs") == 0)
            {
                sin>>ConcurrentJobs;
                continue;
            }
            if (op.compare("CPUCount") == 0)
            {
                sin>>CPUCount;
                continue;
            }
            if (op.compare("CompilerTimeLimit") == 0)
            {
                sin>>CompilerTimeLimit;
                continue;
            }
            if (op.compare("TimeMultipler") == 0)
            {
            	sin>>TimeMultipler;
            	continue;
            }
            if (op.compare("WaitlistSize") == 0)
            {
                sin>>WaitlistSize;
                continue;
            }
            getline(sin,param);
            
            if (op.compare("WorkingDirPrefix") == 0)
            {
                WorkingDirPrefix = param;
                continue;
            }
            if (op.compare("DataDirPrefix") == 0)
            {
                DataDirPrefix = param;
                continue;
            }
            
            if (op.compare("TempDirPrefix") == 0)
            {
                TempDirPrefix = param;
                continue;
            }
            
            if (op.compare("ProblemSchemaDB") == 0)
            {
                ProblemSchemaDB = param;
                continue;
            }
            if (op.compare("Token") == 0)
            {
            	Token = param;
            	continue;
            }
            if (op.compare("WebServer") == 0)
            {
            	WebServer = param;
            	continue;
            }
            if (op.compare("WebServerCallbackScript") == 0)
            {
            	WebServerCallbackScript = param;
            	continue;
            }
        }
    }
}
