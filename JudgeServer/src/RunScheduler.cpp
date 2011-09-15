//
//  RunScheduler.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 9/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "RunScheduler.h"

int RunScheduler::arrangeTask(JudgeRecord* record)
{
	JudgeRecord *currentRecord;
	if (runningJobs >= concurrency)
	{
		if (waitlist.size() >= waitlistCapacity)
		{
			return 1001;
		}
		waitlist.push(record);
		return -1;
	}
	else
	{
		if (waitlist.empty())currentRecord = record;
		else
		{
			currentRecord = waitlist.front();
			waitlist.pop();
			waitlist.push(record);
		}
		runTask(currentRecord);
	}
	return 0;
}

void RunScheduler::runTask(JudgeRecord *record)
{
	runningJobs++;

	pid_t pid = fork();
	if (pid == 0)
	{
		record->judge();
		WebServer server;
		server.pushResult(record);
		delete record;
		exit(0);
	}
	else
	{
		int minSize = 1<<30;
		int mini;
		for (int i=0;i<cpus.size();i++)
		{
			if (cpus.at(i) < minSize)
			{
				minSize = cpus.at(i);
				mini = i;
			}
		}
		cpus[mini]++;

		cpu_set_t cpumask;

		CPU_ZERO(&cpumask);
		CPU_SET(mini,&cpumask);

		sched_setaffinity(pid,sizeof(cpu_set_t),&cpumask);
		delete record;
	}
}

void RunScheduler::removeTask(int cpuid)
{
	runningJobs--;
	cpus[cpuid]--;
	if (!waitlist.empty())
	{
		JudgeRecord *record = waitlist.front();
		waitlist.pop();
		runTask(record);
	}
}

bool RunScheduler::serverBusy()
{
	return waitlist.size() >= waitlistCapacity && runningJobs >= concurrency;
}

int RunScheduler::serverWorkload()
{
	return waitlist.size() + runningJobs;
}
