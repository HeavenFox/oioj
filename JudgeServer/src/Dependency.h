/*
 * Dependency.h
 *
 *  Created on: Sep 17, 2011
 *	  Author: zhujingsi
 */

#ifndef _DEPENDENCY_H_
#define _DEPENDENCY_H_
#include <string>
#include "sqlite3.h"

using namespace std;

class Dependency {
public:
	string filename;
	int type;

	void addSchema(sqlite3 *db, int problemID);
};


#endif /* DEPENDENCY_H_ */
