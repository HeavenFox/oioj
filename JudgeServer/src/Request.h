/* 
 * File:   Request.h
 * Author: zhujingsi
 *
 * Created on March 3, 2012, 2:15 PM
 */

#ifndef REQUEST_H
#define	REQUEST_H

#include "rapidxml.hpp"

using namespace rapidxml;

class Request
{
public:
	virtual void parse(xml_node<> *request) = 0;
};

#endif	/* REQUEST_H */

