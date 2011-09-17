//
//  Compiler.h
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/25/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//
#ifndef _COMPILER_H_
#define _COMPILER_H_

#include <string>
#include <exception>
#include <cstdio>

using namespace std;

class CompilerException : exception
{
    
};

class Compiler
{
public:
	string message;
	bool success;
    virtual void compile(string &input, string &output, double tl)
    {
    	FILE *mes = popen(generateCommand(input,output).c_str(),"r");
    	success = true;
    	char buffer[250];
    	while (!feof(mes))
    	{
    		fgets(buffer,250,mes);
    		message.append(buffer);
    	}
    	if (pclose(mes)/256)
    	{
    		success = false;
    	}
    }
private:
    virtual string generateCommand(const string &input, const string &output) = 0;
};

#endif
