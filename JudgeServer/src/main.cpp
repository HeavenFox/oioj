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

using namespace std;

#define REQUEST_EVAL 1
#define REQUEST_ADD 2

RunScheduler *scheduler;

int msgQueue;

int main (int argc, const char * argv[])
{
    cout<<"JudgeServer Initiating"<<endl;
    cout<<"Reading configuration... "<<endl;
    
    Configuration::ReadConfiguration();

    msgQueue = msgget(IPC_PRIVATE, 0700);

    // Create socket
    int sock = socket(AF_INET, SOCK_STREAM, 0);
    
    if (sock < 0)
    {
        perror("Create");
    }
    
    fcntl(sock,F_SETFL,O_NONBLOCK);

    sockaddr_in sock_address, client_sock_addr;
    socklen_t client_sock_addr_len;
    memset((void*)&sock_address, 0, sizeof(sockaddr_in));
    
    sock_address.sin_family = AF_INET;
    sock_address.sin_addr.s_addr = INADDR_ANY;
    sock_address.sin_port = htons(Configuration::PortNumber);
    
    if (bind(sock, (struct sockaddr*)&sock_address, sizeof(sockaddr_in)) < 0)
    {
        perror("Error: bind");
    }
    
    cout<<"Listening to connection at "<<Configuration::PortNumber<<endl;
    
    listen(sock, 5);
    
    scheduler = new RunScheduler(Configuration::CPUCount, Configuration::ConcurrentJobs, Configuration::WaitlistSize);
    
    signal(SIGCHLD, SIG_IGN);
    
    while (true)
    {
    	int client_sock;
    	struct msgEval msg;
    	while ((client_sock = accept(sock, (struct sockaddr*)&client_sock_addr, &client_sock_addr_len)) < 0)
    	{
    		// Check message queue
    		while (msgrcv(msgQueue,&msg,sizeof(msg),0,IPC_NOWAIT) >= 0)
    		{
    			scheduler->removeTask(msg.cpuid);
    		}
    	}
    	cout<<"Received request"<<endl;
        if (scheduler->serverBusy())
        {
            char failure[20];
            sprintf(failure,"%d\n%d", 1001, scheduler->serverWorkload());
            send(client_sock,failure,strlen(failure),0);
        }
        else
        {
            const int buffer_size = 500;
            const int command_size = 6;
            char actioncmd[command_size+1];
            int len = recv(client_sock, actioncmd, command_size, 0);
            actioncmd[len] = '\0';
            int action;
            if (strcmp(actioncmd,"JUDGE\n") == 0)
            {
            	action = REQUEST_EVAL;
            }
            else if (strcmp(actioncmd,"ADDPB\n") == 0)
            {
            	action = REQUEST_ADD;
            }
            else
            {
            	cerr<<"unrecognized command: "<<actioncmd<<endl;
            	close(client_sock);
            	continue;
            }


            char buffer[buffer_size+1];
            
            string str;
            
            while (long bytes_recv = recv(client_sock, buffer, buffer_size, 0))
            {
                buffer[bytes_recv] = '\0';
                str.append(buffer);
            }
            
            if (action == REQUEST_EVAL)
            {
            	JudgeRecord *currentRecord = new JudgeRecord;

            	if (!currentRecord->prepareProblem(str))
            	{
            		delete currentRecord;
            	} else
            	{
            		int code = scheduler->arrangeTask(currentRecord);
            	}

            } else if (action == REQUEST_ADD)
            {
            	AddRequest req;
            	req.processRequest(str);
            }
        }
        
        close(client_sock);
    }
    
    close(sock);
    
    return 0;
}

