/* 
 * File:   Sandbox.h
 * Author: zhujingsi
 *
 * Created on March 2, 2012, 9:04 PM
 */

#ifndef _SANDBOX_H_
#define	_SANDBOX_H_

class Sandbox
{
public:
	double timeLimit;
	int memoryLimit;
	string workingDir;
	bool chroot;
	int outputLimit;
	bool watchSystemCall;
	int uid;
};

#endif	/* SANDBOX_H */

