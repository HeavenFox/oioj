#include "fileop.h"

inline char base64chr(char c)
{
	if (c == '+')return 62;
	if (c == '/')return 63;
	if (c >= 'A' && c <= 'Z')return c-'A';
	if (c >= 'a' && c <= 'z')return 26+c-'a';
	if (c >= '0' && c <= '9')return 52+c-'0';
	return -1;
}

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

void writeBase64(string path,string &content)
{
	FILE *out = fopen(path.c_str(), "w");
	for (unsigned int i=0;i<content.size();i+=4)
	{
		putc((base64chr(content.at(i))<<2)+((base64chr(content.at(i+1))&0x30)>>4), out);
		if (base64chr(content.at(i+2)) >= 0)
			putc(((base64chr(content.at(i+1))&0x0F)<<4)+((base64chr(content.at(i+2))&0x3C)>>2), out);
		if (base64chr(content.at(i+3)) >= 0)
			putc(((base64chr(content.at(i+2))&0x03)<<6)+base64chr(content.at(i+3)), out);
	}
	fclose(out);
}

void trim(string& s)
{
	while (s.at(s.size()-1) == '\n' || s.at(s.size()-1) == '\r')
		s.resize(s.size()-1);
}
