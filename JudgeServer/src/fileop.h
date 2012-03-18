/*
 * fileop.h
 *
 *  Created on: Sep 17, 2011
 *	  Author: zhujingsi
 */

#ifndef _FILEOP_H_
#define _FILEOP_H_
#include <sys/unistd.h>
#include <sys/wait.h>
#include <string>
#include <cstdio>
#include <cstring>

using namespace std;

void cp(const char* old, const char* target);

void writeBase64(string path,string &content);

void writeBase64(const char* path,const char* content,unsigned int size = 0);

void trim(string& s);

#endif /* FILEOP_H_ */
