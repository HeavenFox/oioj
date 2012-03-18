//
//  main.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/13/11.
//  Copyright 2011 HeavenFox's Labs. All rights reserved.
//

#include <iostream>
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/ipc.h>
#include <sys/msg.h>
#include <sys/signal.h>
#include <arpa/inet.h>
#include <string>
#include <string.h>

#include "ExecutionServer.cpp"

#include "AddRequest.h"

#include "Configuration.h"
#include "JudgeRecord.h"
#include "RunScheduler.h"
#include "msgEval.h"
#include <syslog.h>

using namespace std;

#define REQUEST_EVAL 1
#define REQUEST_ADD 2
#define REQUEST_STATS 3

#define SERVERCODE_SUCCESS 0
#define SERVERCODE_WAITLISTED 1
#define SERVERCODE_FULL 2
#define SERVERCODE_INTERNAL 3

RunScheduler *scheduler;

int msgQueue;

int main (int argc, const char * argv[])
{
	// Daemonize
	daemon(0,0);
	umask(0);
	// Finish daemonizing
	
	openlog("oiojd",LOG_PID,LOG_DAEMON);
	syslog(LOG_INFO, "OIOJ Judge Daemon Starting");

	msgQueue = msgget(IPC_PRIVATE, 0700);

	// Create socket
	int sock = socket(AF_INET, SOCK_STREAM, 0);
	
	if (sock < 0)
	{
		syslog(LOG_ERR,"Error creating socket connection");
		exit(1);
	}

	sockaddr_in sock_address, client_sock_addr;
	socklen_t client_sock_addr_len;
	memset((void*)&sock_address, 0, sizeof(sockaddr_in));
	
	ConfigFile* config = Configuration::Get();
	
	sock_address.sin_family = AF_INET;
	sock_address.sin_addr.s_addr = INADDR_ANY;
	sock_address.sin_port = htons(config->read<int>("port"));
	
	if (bind(sock, (struct sockaddr*)&sock_address, sizeof(sockaddr_in)) < 0)
	{
		syslog(LOG_ERR,"Error binding");
		exit(1);
	}
	
	
	listen(sock, 5);
	
	syslog(LOG_INFO,"Listening for incoming connection at %d...",config->read<int>("port"));
	
	scheduler = new RunScheduler(config->read<int>("cpu_count"), config->read<int>("concurrent_jobs"), config->read<int>("waitlist_size"));
	
	timeval timeout;

	fd_set fds;

	signal(SIGCHLD, SIG_IGN);
	
	while (true)
	{
		FD_ZERO(&fds);
		FD_SET(sock,&fds);
		timeout.tv_sec = 5;
		timeout.tv_usec = 0;
		int client_sock;
		struct msgEval msg;

		int selResult = select(sock+1,&fds,&fds,NULL,&timeout);

		if (selResult == -1)
		{
			syslog(LOG_ERR, "Error selecting file descriptor");
			continue;
		}

		// Check message queue
		while (msgrcv(msgQueue,&msg,sizeof(msg),0,IPC_NOWAIT) >= 0)
		{
			scheduler->removeTask(msg.cpuid);
		}
		
		if (selResult == 0)
		{
			continue;
		}

		client_sock = accept(sock, (struct sockaddr*)&client_sock_addr, &client_sock_addr_len);

		syslog(LOG_INFO, "Request received");
		
		FILE *sockfile = fdopen(client_sock,"r+");
		
		int reqlength;
		
		fread(&reqlength,sizeof(int),1,sockfile);
		
		// Discard too big requests
		if (reqlength < 8*1024*1024)
		{
			char *buffer = new char[reqlength + 1];
			fread(buffer,sizeof(char),reqlength,sockfile);
			buffer[reqlength] = '\0';

			syslog(LOG_INFO, "%s", buffer);

			xml_document<> req;

			req.parse<0>(buffer);

			xml_node<> *rootnode = req.first_node();
			
			// Authenticate
			xml_attribute<> *tokenNode = rootnode->first_attribute("token");
			xml_attribute<> *backupTokenNode = rootnode->first_attribute("backup-token");
			
			const char* token = config->read<string>("token").c_str();
			
			if ((tokenNode && strcmp(tokenNode->value(), token) == 0) || (backupTokenNode && strcmp(backupTokenNode->value(), token) == 0))
			{
				xml_attribute<> *type = rootnode->first_attribute("type");

				if (strcmp(type->value(),"judge") == 0)
				{
					syslog(LOG_INFO, "Request type: judge");
					JudgeRecord *currentRecord = new JudgeRecord;

					char response[512];
					int code;

					try
					{
						currentRecord->parse(rootnode);
					}
					catch(int e)
					{
						syslog(LOG_ERR, "Failed to parse problem.");
						code = SERVERCODE_INTERNAL;
						delete currentRecord;
					}

					code = scheduler->arrangeTask(currentRecord);
					sprintf(response, "<response code=\"%d\" workload=\"%d\" />\n", code, scheduler->serverWorkload());
					send(client_sock,response,strlen(response),0);
				}
				else if (strcmp(type->value(),"addproblem") == 0)
				{
					syslog(LOG_INFO, "Request type: add problem");
					AddRequest req;
					req.parse(rootnode);
				}
				else if (strcmp(type->value(),"status") == 0)
				{
					char response[512];
					sprintf(response, "<response workload=\"%d\" />\n", scheduler->serverWorkload());
					send(client_sock,response,strlen(response),0);
				}
				else
				{
					syslog(LOG_ERR,"unrecognized command: %s", type->value());
					char response[512];
					sprintf(response, "<response error=\"405\" />\n", scheduler->serverWorkload());
					send(client_sock,response,strlen(response),0);
				}
			}

			delete buffer;
			syslog(LOG_INFO, "Request processed");
		}
		else
		{
			syslog(LOG_ERR, "Request too big: %d bytes, discarded", reqlength);
		}
		
		
		close(client_sock);
	}
	
	close(sock);
	
	return 0;
}

