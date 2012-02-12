//
//  Compiler_CPP.h
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/25/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//
#ifndef _COMPILER_PAS_H_
#define _COMPILER_PAS_H_

#include <cstdlib>
#include "Compiler.h"

using namespace std;

class Compiler_PAS : public Compiler
{
private:
	string generateCommand(const string &input, const string &output);
};

#endif
