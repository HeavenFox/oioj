//
//  TestCase.h
//  JudgeServer
//
//  Created by Zhu Jingsi on 9/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#ifndef _TESTCASE_H_
#define _TESTCASE_H_

#include <string>
#include <string.h>
#include <sys/ptrace.h>
#include <sys/signal.h>
#include <cmath>
#include <cstring>
#include <syslog.h>

#include "JudgeRecord.h"
#include "fileop.h"
#include "sqlite3.h"

using namespace std;

#define TESTRESULT_INDETERMINE 0
#define TESTRESULT_TLE 1
#define TESTRESULT_MLE 2
#define TESTRESULT_OLE 3
#define TESTRESULT_SYSCALL 4
#define TESTRESULT_RTE 5
#define TESTRESULT_WA 6
#define TESTRESULT_OK 7

#define OUTPUT_LIMIT 256

class JudgeRecord;

class TestCase
{
public:
    int caseID;
    int score;
    double timeLimit,actualTime;
    int memoryLimit;
    int bytesActualMemory;
    int problemID;
    
    string input;
    string answer;
    
    JudgeRecord *record;
    
    int result;
    int resultExtended;
    
    void run();
    
    void compare();

    void loadSchema();

    void addSchema(sqlite3* db);

    void cleanup();

private:
    short callAllowance[512];

    char inputDataPath[256];
    char outputDataPath[256];

    void initCallAllowance();
};
#endif
