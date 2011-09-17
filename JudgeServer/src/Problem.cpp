/*
 * Problem.cpp
 *
 *  Created on: Sep 15, 2011
 *      Author: zhujingsi
 */

#include "Problem.h"

void Problem::addSchema(sqlite3* db)
{
	char query[] = "INSERT INTO `problems` (id,type,compare,input,output) VALUES (?,?,?,?,?)";

	sqlite3_stmt *stmt;
	sqlite3_prepare_v2(db,query,sizeof(query),&stmt,NULL);
	sqlite3_bind_int(stmt,1,id);
	sqlite3_bind_int(stmt,2,type);
	sqlite3_bind_text(stmt,3,compare.c_str(),compare.size(),NULL);
	sqlite3_bind_text(stmt,4,input.c_str(),input.size(),NULL);
	sqlite3_bind_text(stmt,5,output.c_str(),output.size(),NULL);
	sqlite3_step(stmt);
	sqlite3_finalize(stmt);
}
