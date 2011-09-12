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
#include <sys/msg.h>
#include <sys/signal.h>
#include <arpa/inet.h>
#include <string>
#include <string.h>

#include "ExecutionServer.cpp"

#include "Configuration.h"
#include "JudgeRecord.h"
#include "RunScheduler.h"

#include "constants.h"

using namespace std;

RunScheduler *scheduler;

void evalFinished(int signal, siginfo_t* cpuid, void* dummy)
{
	scheduler->removeTask(cpuid->si_value.sival_int);
}

int main (int argc, const char * argv[])
{
    cout<<"JudgeServer Initiating"<<endl;
    cout<<"Reading configuration... "<<endl;
    
    Configuration::ReadConfiguration();
    
    scheduler = new RunScheduler(Configuration::CPUCount, Configuration::ConcurrentJobs, Configuration::WaitlistSize);


    // Prepare signal
    struct sigaction act;
    act.sa_sigaction = evalFinished;
    act.sa_flags = SA_SIGINFO;


    signal(SIGCHLD, SIG_IGN);
    sigaction(SIG_EVALFINISH,&act,NULL);

    // Create socket
    int sock = socket(AF_INET, SOCK_STREAM, 0);
    
    if (sock < 0)
    {
        perror("Create");
    }
    
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
    
    
    while (true)
    {
        int client_sock = accept(sock, (struct sockaddr*)&client_sock_addr, &client_sock_addr_len);

        if (scheduler->serverBusy())
        {
            char failure[20];
            sprintf(failure,"%d\n%d", 1001, scheduler->serverWorkload());
            send(client_sock,failure,strlen(failure),0);
        }
        else
        {
            const int buffer_size = 500;
            char buffer[buffer_size+1];
            
            string str;
            
            while (long bytes_recv = recv(client_sock, buffer, buffer_size, 0))
            {
                buffer[bytes_recv] = '\0';
                str.append(buffer);
                cout<<"got some "<<buffer<<endl;
            }
            

            JudgeRecord *currentRecord = new JudgeRecord;

            currentRecord->prepareProblem(str);
            scheduler->arrangeTask(currentRecord);
        }
        
        close(client_sock);
    }
    
    close(sock);
    
    return 0;
}

