//
//  Compiler_CPP.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 9/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "Compiler_PAS.h"

string Compiler_PAS::generateCommand(const string &input, const string &output)
{
    string command("fpc ");
    command.append(input);
    command.append(" -o");
    command.append(output);
    return command;
}
