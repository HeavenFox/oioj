/*
 * AddRequest.h
 *
 *  Created on: Sep 17, 2011
 *      Author: zhujingsi
 */

#ifndef _ADDREQUEST_H_
#define _ADDREQUEST_H_
#include "sqlite3.h"
#include "Configuration.h"
#include "Problem.h"
#include "TestCase.h"
#include "Dependency.h"
#include <sstream>

class AddRequest
{
public:
	void processRequest(string &s);
};


#endif /* ADDREQUEST_H_ */
