//
//  JudgeRecord.h
//  JudgeServer
//
//  Created by Zhu Jingsi on 9/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#ifndef _JUDGERECORD_H_
#define _JUDGERECORD_H_

#include <iostream>
#include <sstream>
#include <vector>

#include <sys/user.h>
#include <sys/types.h>
#include <sys/time.h>
#include <sys/resource.h>
#include <sys/wait.h>
#include <sys/ptrace.h>
#include <sys/syscall.h>
#include <sys/stat.h>

#include "sqlite3.h"
#include "Configuration.h"
#include "Compiler_C.h"
#include "Compiler_CPP.h"
#include "TestCase.h"

using namespace std;

#define DEPENDENCY_COMPILE 1
#define DEPENDENCY_RUN 2

#define PROBLEMTYPE_CLASSIC 1
#define PROBLEMTYPE_OUTPUT 2
#define PROBLEMTYPE_INTERACTIVE 3


#define RECORDSTATUS_ACCEPTED 2
#define RECORDSTATUS_CE 3
#define RECORDSTATUS_REJECTED 4

#define COMPARE_FULLTEXT "/FULLTEXT/"
#define COMPARE_OMITSPACE "/OMITSPACE/"

class TestCase;

struct Dependency {
    string filename;
    int type;
};

class JudgeRecord
{
private:
    void compile();
    
    void loadProblemSchema();
    
    inline char base64chr(char c);
    
    Compiler *compiler;
    
    bool deducedVariable;

    void deduceVariable();

public:
    int problemID;
    int recordID;
    
    string language;
    
    string comparison;
    vector<Dependency> dependencies;
    
    string workingDirectory;
    
    string dataDirectory;
    
    string submissionPath;
    
    string input;
    string output;
    
    string binaryPath;
    
    int type;
    
    short status;

    short cpu;

    vector<TestCase> cases;
    
    JudgeRecord()
    {
    	deducedVariable = false;
    }
    
    void prepareProblem(string s);
    
    void setProblemSchema(string s);
    
    void judge();

};
#endif
