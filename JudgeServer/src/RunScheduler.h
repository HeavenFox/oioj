//
//  RunScheduler.h
//  JudgeServer
//
//  Created by Zhu Jingsi on 9/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#ifndef _RUNSCHEDULER_H_
#define _RUNSCHEDULER_H_

#include <list>
#include <queue>
#include "JudgeRecord.h"
#include "WebServer.h"
#include <sys/ipc.h>
#include <sys/msg.h>
#include "msgEval.h"

#define SERVERCODE_SUCCESS 0
#define SERVERCODE_WAITLISTED 1
#define SERVERCODE_FULL 2
#define SERVERCODE_INTERNAL 3

using namespace std;

extern int msgQueue;

class RunScheduler {
    int nCPU;
    int concurrency;
    int waitlistCapacity;
    
    int *cpus;
    queue<JudgeRecord*> waitlist;
    
    int runningJobs;
    
    void runTask(JudgeRecord *record);
    
public:
    RunScheduler(int _nCPU, int _concurrency, int _waitlistCapacity);
    
    ~RunScheduler()
    {
    	delete cpus;
    }

    int arrangeCPU();
    
    int arrangeTask(JudgeRecord* record);
    
    void removeTask(int cpuid);
    
    bool serverBusy();

    int serverWorkload();
};

#endif
