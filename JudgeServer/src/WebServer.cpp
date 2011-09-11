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

	char generalState[256];
	sprintf(generalState, "RecordID %d\nStatus %d\n", recordID, status);

	string postString("general=");
	postString.append(urlencode(generalState));
	for (vector<TestCase>::iterator it = record->cases.begin();it != record->cases.end();it++)
	{

		if ((*it).result != TESTRESULT_OK)
		{
			status = RECORDSTATUS_REJECTED;
		}

		char thiscase[1024];

		sprintf(thiscase, "CaseID %d\nCaseResult %d\nCaseExtendedCode %d\nCaseScore %d\nCaseTime %.2f\nCaseMemory %.2f\n", (*it).caseID, (*it).result, (*it).resultExtended, (*it).score, (*it).actualTime, ((double)(*it).bytesActualMemory)/1024.0/1024.0);
		postString.append("&cases%5B%5D=");
		postString.append(urlencode(thiscase));
	}

	char finalRequest[1500];
	sprintf(finalRequest, "POST %s HTTP/1.1\r\nContent-Type: application/x-www-form-urlencoded\r\nUser-Agent: OIOJJudgeServer/1.0\r\nHost: %s\r\nContent-Length: %d\r\nConnection: Keep-Alive\r\nCache-Control: no-cache\r\n\r\n%s",Configuration::WebServerCallbackScript.c_str(),Configuration::WebServer.c_str(),postString.size(),postString.c_str());
	// Create socket
	int sock = socket(AF_INET, SOCK_STREAM, 0);

	if (sock < 0)
	{
		perror("Create");
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
			perror("gethostbyname");
		}
		sock_address.sin_addr.s_addr =((struct in_addr*)phost->h_addr)->s_addr;
	}
	sock_address.sin_port = htons(80);

	connect(sock,(struct sockaddr*)&sock_address,sizeof(struct sockaddr));

	send(sock,finalRequest,strlen(finalRequest),0);
	close(sock);



}
