/* 
 * File:   CompilerFactory.h
 * Author: zhujingsi
 *
 * Created on March 3, 2012, 8:06 PM
 */

#ifndef COMPILERFACTORY_H
#define	COMPILERFACTORY_H

#include "Compiler.h"
#include "Compiler_C.h"
#include "Compiler_CPP.h"
#include "Compiler_PAS.h"
#include <cstring>

class CompilerFactory
{
public:
	static Compiler* get(const char* lang)
	{
		if (strcmp(lang,"cpp") == 0)
		{
			
		}
	}
};

#endif	/* COMPILERFACTORY_H */

