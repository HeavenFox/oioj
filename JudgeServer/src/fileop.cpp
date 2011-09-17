#include "fileop.h"

void cp(const char* old, const char* target)
{
	pid_t pid = vfork();
	if (pid == 0)
	{
		execl("/bin/cp","cp",old,target,NULL);
	}else
	{
		waitpid(pid,NULL,NULL);
	}
}
