#ifndef JUDGEDEF_H_
#define JUDGEDEF_H_
#include "timer.h"
#include "conv.h"
#include "base64.h"
#include <rapidjson/document.h>
#include <rapidjson/writer.h>
#include <rapidjson/stringbuffer.h>
#include <rapidjson/ostreamwrapper.h>
#include <mysql.h>
#include <unistd.h>
#include <fcntl.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <sys/mman.h>
#include <string>
#include <sstream>
#include <fstream>
#include <vector>
#include <algorithm>
#include <regex>

#define __JUDGER_VERSION 1.1

namespace judge {
void checkMemory(std::string, int&);
enum Status {
	UKE, AC, WA, RE, CE, TLE, MLE, OLE, JG
};
class Monitor {
public:
	int execTime;
	int execMemory;
	int execPid;
	int execId;
	int limitTime;
	int limitMemory;
	bool available;
	std::string inputFile;
	std::string location;
	Status status;
	void start();
	Monitor();
	Monitor(int, int, int, std::string, std::string);
};
class Language {
public:
	bool intLang;
	std::string name;
	std::string sourceSuffix;
	std::string progSuffix;
	std::string compileCommand;
	std::string execCommand;
	bool operator==(const std::string) const;
	Language();
	Language(std::string, std::string, std::string, std::string, std::string,
			bool);
};
class Compiler {
public:
	std::string location;
	std::string information;
	bool success;
	void start(std::string);
	void dump();
	std::vector<Language>::iterator current;
	Compiler(std::string);
private:
	std::string conv(std::string);
};
class Testpoint {
public:
	Status status;
	int time;
	int memory;
	int execTime;
	int execMemory;
	std::string input;
	std::string output;
	std::string comparer;
	std::string info;
	Testpoint();
};
class Judger {
public:
	Status status;
	void start();
	void dump();
	void dump2();
	Judger(std::string);
private:
	int totalTime;
	int totalMemory;
	std::string oj;
	std::string problem;
	std::string submitid;
	std::string location;
	std::string language;
	std::string compileInfo;
	std::vector<Testpoint> data;
	MYSQL conn;
};
std::vector<Language> lang;
}
#include "judger.h"

#endif
