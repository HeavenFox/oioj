//
//  Configuration.cpp
//  JudgeServer
//
//  Created by Zhu Jingsi on 9/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#include "Configuration.h"

ConfigFile* Configuration::Get()
{
	static ConfigFile* file;
	
	if (!file)
	{
		file = new ConfigFile(CONFIG_LOCATION);
	}
	
	return file;
}
