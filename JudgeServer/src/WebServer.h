//
//  WebServer.h
//  JudgeServer
//
//  Created by Zhu Jingsi on 9/2/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#ifndef _WEBSERVER_H_
#define _WEBSERVER_H_
#include <sys/types.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <netdb.h>
#include <fcntl.h>
#include <unistd.h>
#include <errno.h>
#include <netinet/in.h>
#include "JudgeRecord.h"
#include "rapidxml.hpp"
#include "rapidxml_print.hpp"

using namespace rapidxml;

class WebServer
{
public:
	void pushResult(JudgeRecord *record);
};

#endif
