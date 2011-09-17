/*
 * fileop.h
 *
 *  Created on: Sep 17, 2011
 *      Author: zhujingsi
 */

#ifndef _FILEOP_H_
#define _FILEOP_H_
#include <sys/unistd.h>
#include <sys/wait.h>
#include <string>
#include <cstdio>

using namespace std;

void cp(const char* old, const char* target);

void writeBase64(string path,string &content);

#endif /* FILEOP_H_ */
