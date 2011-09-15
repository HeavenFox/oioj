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

using namespace std;

class RunScheduler {
    int nCPU;
    int concurrency;
    int waitlistCapacity;
    
    vector<int> cpus;
    queue<JudgeRecord*> waitlist;
    
    int runningJobs;
    
    void runTask(JudgeRecord *record);
    
public:
    RunScheduler(int _nCPU, int _concurrency, int _waitlistCapacity)
    {
        nCPU = _nCPU;
        concurrency = _concurrency;
        waitlistCapacity = _waitlistCapacity;
        
        cpus.resize(nCPU);
        
        runningJobs = 0;
        
    }
    
    int arrangeCPU();
    
    int arrangeTask(JudgeRecord* record);
    
    void removeTask(int cpuid);
    
    bool serverBusy();

    int serverWorkload();
};

#endif
