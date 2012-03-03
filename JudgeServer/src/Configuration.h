//
//  Configuration.h
//  JudgeServer
//
//  Created by Zhu Jingsi on 8/30/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#ifndef _CONFIGURATION_H_
#define _CONFIGURATION_H_

#define CONFIG_LOCATION "/etc/oioj/oioj.conf"

#include "ConfigFile.h"

using namespace std;

class Configuration {
public:
	static ConfigFile* Get();
};

#endif
