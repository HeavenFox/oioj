//
//  Compiler_C.h
//  JudgeServer
//
//  Created by Zhu Jingsi on 9/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//
#ifndef _COMPILER_C_H_
#define _COMPILER_C_H_

#include "Compiler.h"
#include <cstdlib>

using namespace std;

class Compiler_C : public Compiler
{
private:
	string generateCommand(const string &input, const string &output);
};

#endif
