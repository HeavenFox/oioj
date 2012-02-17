//
//  WebServer.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/30/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "WebServer.h"


string urlencode(char* str)
{
	string s;
	char *c = str;
	while (*c != '\0')
	{
		if (((*c) >= 'a' && (*c) <= 'z') || ((*c) >= 'A' && (*c) <= 'Z') || ((*c) >= '0' && (*c) <= '9'))
		{
			s.append(1,*c);
		}else if (*c == ' ')
		{
			s.append("+");
		}else
		{
			char hex[5];
			sprintf(hex,"%%%02X",(int)(*c));
			s.append(hex);
		}
		c++;
	}
	return s;
}

void WebServer::pushResult(JudgeRecord *record)
{
	int recordID = record->recordID;
	int status = RECORDSTATUS_ACCEPTED;
	
	syslog(LOG_INFO,"Pushing record %d to web server", recordID);
	
	char generalState[1024];
	
	for (vector<TestCase>::iterator it = record->cases.begin();it != record->cases.end();it++)
	{
		if ((*it).result != TESTRESULT_OK)
		{
			status = RECORDSTATUS_REJECTED;
		}
	}
	
	sprintf(generalState, "RecordID %d\nStatus %d\nToken %s\n", recordID, status, Configuration::Token.c_str());

	string postString("general=");
	postString.append(urlencode(generalState));
	for (vector<TestCase>::iterator it = record->cases.begin();it != record->cases.end();it++)
	{
		char thiscase[1024];

		sprintf(thiscase, "CaseID %d\nCaseResult %d\nCaseExtendedCode %d\nCaseScore %d\nCaseTime %.2f\nCaseMemory %.2f\n", (*it).caseID, (*it).result, (*it).resultExtended, (*it).score, (*it).actualTime, ((double)(*it).bytesActualMemory)/1024.0/1024.0);
		postString.append("&cases%5B%5D=");
		postString.append(urlencode(thiscase));
	}

	ostringstream finalRequest;
	
	finalRequest<<"POST "<<Configuration::WebServerCallbackScript<<" HTTP/1.1\r\nContent-Type: application/x-www-form-urlencoded\r\nUser-Agent: OIOJJudgeServer/1.0\r\nHost: "<<Configuration::WebServer<<"\r\nContent-Length: "<<postString.size()<<"\r\nConnection: Keep-Alive\r\nCache-Control: no-cache\r\n\r\n"<<postString;
	// Create socket
	int sock = socket(AF_INET, SOCK_STREAM, 0);

	if (sock < 0)
	{
		syslog(LOG_ERR,"Error creating socket connection to web server");
	}

	sockaddr_in sock_address;
	hostent *phost;
	memset((void*)&sock_address, 0, sizeof(sockaddr_in));

	sock_address.sin_family = AF_INET;
	sock_address.sin_addr.s_addr = inet_addr(Configuration::WebServer.c_str());
	if (sock_address.sin_addr.s_addr == INADDR_NONE)
	{
		phost = (struct hostent*)gethostbyname(Configuration::WebServer.c_str());
		if(phost == NULL){
			syslog(LOG_ERR,"Error parsing server: %d", errno);
		}
		sock_address.sin_addr.s_addr =((struct in_addr*)phost->h_addr)->s_addr;
	}
	sock_address.sin_port = htons(80);

	if (connect(sock,(struct sockaddr*)&sock_address,sizeof(struct sockaddr)) < 0)
	{
		syslog(LOG_ERR,"Unable to connect to server: %d", errno);
	}
	else
	{
		send(sock,finalRequest.str().c_str(),finalRequest.str().size(),0);
		close(sock);
	}
	syslog(LOG_INFO,"record %d pushed", recordID);
}
