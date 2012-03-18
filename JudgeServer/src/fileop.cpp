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
		waitpid(pid,NULL,0);
	}
}

void writeBase64(const string path,const string &content)
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

void writeBase64(const char* path,const char* content,unsigned int size)
{
	FILE *out = fopen(path, "w");
	
	if (size == 0)
	{
		size = strlen(content);
	}
	const char *ptr = content;
	
	for (unsigned int i=0; i<size; i += 4,ptr += 4)
	{
		putc((base64chr(*ptr)<<2)+((base64chr(*(ptr+1))&0x30)>>4), out);
		if (base64chr(*(ptr+2)) >= 0)
			putc(((base64chr(*(ptr+1))&0x0F)<<4)+((base64chr(*(ptr+2))&0x3C)>>2), out);
		if (base64chr(*(ptr+3)) >= 0)
			putc(((base64chr(*(ptr+2))&0x03)<<6)+base64chr(*(ptr+3)), out);
	}
	fclose(out);
}

void trim(string& s)
{
	while (s.at(s.size()-1) == '\n' || s.at(s.size()-1) == '\r')
		s.resize(s.size()-1);
}
