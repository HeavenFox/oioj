/*
 * Problem.h
 *
 *  Created on: Sep 15, 2011
 *	  Author: zhujingsi
 */

#ifndef _PROBLEM_H_
#define _PROBLEM_H_

#include "sqlite3.h"
#include <string>
#include <syslog.h>

using namespace std;

class Problem
{
public:
	int id;

	string input;
	string output;

	int type;

	string compare;

	void loadSchema();
	void addSchema(sqlite3 *db);
};


#endif /* PROBLEM_H_ */
