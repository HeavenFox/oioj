/*
 * Problem.h
 *
 *  Created on: Sep 15, 2011
 *      Author: zhujingsi
 */

#ifndef _PROBLEM_H_
#define _PROBLEM_H_

#include "sqlite3.h"
#include <string>

using namespace std;

class Problem
{
public:
	int id;

	string input;
	string output;

	short type;

	string compare;

	void loadSchema();
	void addSchema(sqlite3 *db);

private:
	sqlite3 *sqlite;
};


#endif /* PROBLEM_H_ */
