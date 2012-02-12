/*
 * Dependency.cpp
 *
 *  Created on: Sep 17, 2011
 *	  Author: zhujingsi
 */

#include "Dependency.h"

void Dependency::addSchema(sqlite3 *db, int problemID)
{
	char query[] = "INSERT INTO `dependencies` (pid,filename,type) VALUES (?,?,?)";

	sqlite3_stmt *stmt;
	sqlite3_prepare_v2(db,query,sizeof(query),&stmt,NULL);
	sqlite3_bind_int(stmt,1,problemID);
	sqlite3_bind_text(stmt,2,filename.c_str(),filename.size(),NULL);
	sqlite3_bind_int(stmt,3,type);
	sqlite3_step(stmt);
	sqlite3_finalize(stmt);
}


