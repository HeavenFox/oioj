//
//  Compiler_C.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/25/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "Compiler_C.h"

string Compiler_C::generateCommand(const string &input, const string &output)
{
	string command("gcc -static ");
	command.append(input);
	command.append(" -o ");
	command.append(output);
	return command;
}
