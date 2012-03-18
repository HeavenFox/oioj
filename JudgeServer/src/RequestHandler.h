/* 
 * File:   RequestHandler.h
 * Author: zhujingsi
 *
 * Created on March 3, 2012, 1:49 PM
 */

#ifndef _REQUESTHANDLER_H_
#define	_REQUESTHANDLER_H_
#include <cstring>
#include <string>
#include "rapidxml.hpp"

using namespace std;
using namespace rapidxml;

class RequestHandler
{
public:
	void dispatch(const string& request);
};

#endif	/* REQUESTHANDLER_H */

