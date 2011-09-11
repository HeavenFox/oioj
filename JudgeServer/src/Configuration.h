//
//  Configuration.h
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/30/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#ifndef _CONFIGURATION_H_
#define _CONFIGURATION_H_

#include <fstream>
#include <sstream>

using namespace std;

class Configuration {
public:
    static int PortNumber;
    static int AgentUID;
    static int ConcurrentJobs;
    static int CPUCount;
    static double CompilerTimeLimit;
    static int WaitlistSize;
    static string WorkingDirPrefix;
    static string DataDirPrefix;
    static string ProblemSchemaDB;
    
    static string WebServer;
    static string WebServerCallbackScript;

    static void ReadConfiguration();
};

#endif
