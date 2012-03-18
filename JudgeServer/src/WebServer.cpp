//
//  WebServer.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/30/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "WebServer.h"

#define BASE10_MAX_LENGTH 10
#define BASE10_MAX_LENGTH_FLOAT 20

string urlencode(const char* str)
{
	string s;
	const char *c = str;
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
	
	xml_document<> doc;
	xml_node<> *callbackNode = doc.allocate_node(node_element,"callback");
	callbackNode->append_attribute(doc.allocate_attribute("version","2.0"));
	callbackNode->append_attribute(doc.allocate_attribute("type","judge"));
	
	ConfigFile* config = Configuration::Get();
	string token = config->read<string>("token");
	callbackNode->append_attribute(doc.allocate_attribute("token",token.c_str()));
	
	xml_node<> *recordNode = doc.allocate_node(node_element,"record");
	
	char recordIDString[BASE10_MAX_LENGTH];
	char statusString[BASE10_MAX_LENGTH];
	sprintf(recordIDString,"%d",recordID);
	
	for (vector<TestCase>::iterator it = record->cases.begin();it != record->cases.end();it++)
	{
		if ((*it).result != TESTRESULT_OK)
		{
			status = RECORDSTATUS_REJECTED;
		}
	}
	
	sprintf(statusString, "%d", status);
	
	recordNode->append_attribute(doc.allocate_attribute("id",recordIDString));
	recordNode->append_attribute(doc.allocate_attribute("status",statusString));
	callbackNode->append_node(recordNode);
	
	xml_node<> *casesNode = doc.allocate_node(node_element,"cases");
	
	for (vector<TestCase>::iterator it = record->cases.begin();it != record->cases.end();it++)
	{
		xml_node<> *curCaseNode = doc.allocate_node(node_element, "case");
		
		char *caseID = doc.allocate_string(0,BASE10_MAX_LENGTH);
		sprintf(caseID,"%d",(*it).caseID);
		curCaseNode->append_attribute(doc.allocate_attribute("id",caseID));
		
		char *caseResult = doc.allocate_string(0,BASE10_MAX_LENGTH);
		sprintf(caseResult,"%d",(*it).result);
		curCaseNode->append_attribute(doc.allocate_attribute("result",caseResult));
		
		char *detail = doc.allocate_string((*it).detail.c_str());
		curCaseNode->append_attribute(doc.allocate_attribute("detail",detail));
		
		char *caseScore = doc.allocate_string(0,BASE10_MAX_LENGTH);
		sprintf(caseScore,"%d",(*it).score);
		curCaseNode->append_attribute(doc.allocate_attribute("score",caseScore));
		
		char *caseTime = doc.allocate_string(0,BASE10_MAX_LENGTH_FLOAT);
		sprintf(caseTime,"%.3lf",(*it).actualTime);
		curCaseNode->append_attribute(doc.allocate_attribute("time",caseTime));
		
		char *caseMemory = doc.allocate_string(0,BASE10_MAX_LENGTH_FLOAT);
		sprintf(caseMemory,"%.3lf",((double)(*it).bytesActualMemory)/1024.0/1024.0);
		curCaseNode->append_attribute(doc.allocate_attribute("memory",caseMemory));
		
		casesNode->append_node(curCaseNode);
	}
	
	callbackNode->append_node(casesNode);
	
	doc.append_node(callbackNode);
	
	ostringstream xmlStream;
	xmlStream<<doc;
	
	string postString("data=");
	postString += urlencode("<?xml version=\"1.0\"?>\n");
	postString += urlencode(xmlStream.str().c_str());

	ostringstream requestHeader;
	
	requestHeader<<"POST "<<config->read<string>("webserver_callback")<<" HTTP/1.1\r\nContent-Type: application/x-www-form-urlencoded\r\nUser-Agent: OIOJJudgeServer/1.0\r\nHost: "<<config->read<string>("webserver_address")<<"\r\nContent-Length: "<<postString.size()<<"\r\n\r\n";
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
	sock_address.sin_addr.s_addr = inet_addr(config->read<string>("webserver_address").c_str());
	if (sock_address.sin_addr.s_addr == INADDR_NONE)
	{
		phost = (struct hostent*)gethostbyname(config->read<string>("webserver_address").c_str());
		if(phost == NULL){
			syslog(LOG_ERR,"Error parsing server: %d", errno);
		}
		sock_address.sin_addr.s_addr =((struct in_addr*)phost->h_addr)->s_addr;
	}
	sock_address.sin_port = htons(80);
	
	//const int bufferSize = 512;

	if (connect(sock,(struct sockaddr*)&sock_address,sizeof(struct sockaddr)) < 0)
	{
		syslog(LOG_ERR,"Unable to connect to server: %d", errno);
	}
	else
	{
		send(sock,requestHeader.str().c_str(),requestHeader.str().size(),0);
		send(sock,postString.c_str(),postString.size(),0);
		/*
		const char* reqPtr = finalRequest.str().c_str();
		int remaining = finalRequest.str().size();
		while (remaining > 0)
		{
			
			int nextFrame = min(remaining,bufferSize);
			char chunk[bufferSize + 2];
			memcpy(chunk,reqPtr,nextFrame);
			chunk[nextFrame] = '\0';
			syslog(LOG_INFO, "chunk: %s", chunk);
			int sent = (int)send(sock,reqPtr,nextFrame,(nextFrame < remaining ? MSG_MORE : 0));
			reqPtr += sent;
			remaining -= sent;
		}*/
		
		close(sock);
	}
	syslog(LOG_INFO,"record %d pushed", recordID);
}
