#ifndef JUDGER_H_
#define JUDGER_H_

namespace judge {
Monitor::Monitor() {
	execTime = execMemory = execPid = execId = 0;
	limitTime = 2000;
	limitMemory = 524288;
	available = false;
	status = UKE;
}
Monitor::Monitor(int setExecId, int setLimitTime, int setLimitMemory,
		std::string setInputFile, std::string setLocation) {
	execTime = execMemory = execPid = 0;
	execId = setExecId;
	limitTime = setLimitTime;
	limitMemory = setLimitMemory;
	inputFile = setInputFile;
	location = setLocation;
	available = true;
	status = JG;
}
void Monitor::start() {
	if (!available)
		throw "Bad settings";
	if ((execPid = fork()) == 0) {
		setpgid(0, 0);
		int input = open(inputFile.c_str(), O_RDONLY, 0777);
		dup2(input, 0);
		int output = open((location + "/" + udf::conv(execId) + ".out").c_str(),
				O_WRONLY | O_CREAT | O_TRUNC, 0777);
		dup2(output, 1);
		execlp("/bin/bash", "bash", "-c",
				("ulimit -s unlimited && /usr/bin/time -o " + location
						+ "/info -f \"%M %x\" " + location + "/prog").c_str(),
				NULL);
		exit(0);
	} else {
		xszq::Timer timer;
		int currMemory = 0, returnVal;
		std::string execPidStr = udf::conv(execPid);
		timer.start();
		while (true) {
			// Monitor time
			timer.stop();
			if (timer.msec() > limitTime) {
				kill(-execPid, SIGKILL);
				execTime = limitTime;
				status = TLE;
				break;
			}
			// Monitor memory
			checkMemory(execPidStr, currMemory);
			execMemory = std::max(execMemory, currMemory);
			if (execMemory > limitMemory) {
				kill(-execPid, SIGKILL);
				execTime = timer.msec();
				status = MLE;
				break;
			}
			// Monitor existance
			pid_t currentStatus = waitpid(execPid, &returnVal, WNOHANG);
			if (currentStatus == 0) {
				continue;
			} else if (currentStatus == -1) {
				execTime = timer.msec();
				status = RE;
				break;
			} else {
				execTime = timer.msec();
				if (returnVal)
					status = RE;
				else
					status = AC;
				break;
			}
		}
	}
	if (status != AC) {
		return;
	} else {
		int info = open((location + "/info").c_str(), O_RDONLY), length = lseek(
				info, 0, SEEK_END);
		char *detail = (char *) mmap(NULL, length, PROT_READ, MAP_PRIVATE, info,
				0);
		std::stringstream buf;
		std::string in(detail, length);
		buf << in;
		buf >> execMemory;
		close(info);
		if (execMemory > limitMemory)
			status = MLE;
	}
}
Language::Language() {
	intLang = false;
}
Language::Language(std::string setName, std::string setSS, std::string setPS,
		std::string setCompile, std::string setExec, bool setIntLang) {
	intLang = setIntLang;
	sourceSuffix = setSS;
	progSuffix = setPS;
	name = setName;
	compileCommand = setCompile;
	execCommand = setExec;
}
bool Language::operator==(const std::string name) const {
	return this->name == name;
}
Compiler::Compiler(std::string setLocation) {
	location = setLocation;
	success = false;
}
void Compiler::start(std::string currentLang) {
	current = std::find(lang.begin(), lang.end(), currentLang);
	if (current->intLang) {
		success = true;
		return;
	}
	std::string compileCommandTrue = conv(current->compileCommand);
	int compilePid = 0;
	if ((compilePid = fork()) == 0) {
		setpgid(0, 0);
		int compileInformation = open((location + "/" + "cinfo").c_str(),
				O_WRONLY | O_CREAT | O_TRUNC, 0777);
		dup2(compileInformation, 1);
		dup2(compileInformation, 2);
		execlp("/bin/bash", "/bin/bash", "-c",
				("export LC_ALL=C && " + compileCommandTrue).c_str(), NULL);
		exit(0);
	} else {
		int returnVal = 0;
		xszq::Timer timer;
		timer.start();
		while (true) {
			timer.stop();
			if (timer.msec() >= 10000) {
				kill(-compilePid, SIGKILL);
				information = "Compiling time limit exceeded";
			}
			pid_t currentStatus = waitpid(compilePid, &returnVal, WNOHANG);
			if (currentStatus == 0) {
				continue;
			} else if (currentStatus == -1) {
				information = "Unknown compile error";
				return;
			} else {
				int compileInformation = open(
						(location + "/" + "cinfo").c_str(), O_RDONLY), length =
						lseek(compileInformation, 0, SEEK_END);
				char *detail = (char *) mmap(NULL, length, PROT_READ,
						MAP_PRIVATE, compileInformation, 0);
				information = std::string(detail, length);
				close(compileInformation);
				if (returnVal) {
					success = false;
				} else {
					success = true;
				}
				return;
			}
		}
	}
}
std::string Compiler::conv(std::string original) {
	std::regex regSource("%s"), regProg("%p");
	std::string ret = original;
	if (current->sourceSuffix != "")
		ret = std::regex_replace(ret, regSource,
				location + "/source." + current->sourceSuffix);
	else
		ret = std::regex_replace(ret, regSource, location + "/source");
	if (current->progSuffix != "")
		ret = std::regex_replace(ret, regProg,
				location + "/prog." + current->sourceSuffix);
	else
		ret = std::regex_replace(ret, regProg, location + "/prog");
	return ret;
}
void Compiler::dump() {
	for (size_t i = 0; i < lang.size(); ++i) {
		printf("name: %s\n"
				"sourceSuffix: %s\n"
				"progSuffix: %s\n"
				"compile: %s\n"
				"exec: %s\n"
				"intLang: %d\n"
				"\n", lang[i].name.c_str(), lang[i].sourceSuffix.c_str(),
				lang[i].progSuffix.c_str(), lang[i].compileCommand.c_str(),
				lang[i].execCommand.c_str(), lang[i].intLang);
	}
}
Testpoint::Testpoint() {
	time = 1000;
	memory = 131072;
	execTime = execMemory = 0;
	status = JG;
}
Judger::Judger(std::string file) {
	mysql_init (&conn);

	if (!mysql_real_connect(&conn, "127.0.0.1", "kksk", "kkskkksk", "OJ", 0,
			NULL, CLIENT_FOUND_ROWS)) {
		status = UKE;
		return;
	}
	totalTime = 0;
	totalMemory = 0;
	status = JG;

	int configFile = open("conf/compile.json", O_RDONLY);
	char *configData = (char*) mmap(NULL, lseek(configFile, 0, SEEK_END),
			PROT_READ, MAP_PRIVATE, configFile, 0);
	rapidjson::Document config;
	config.Parse(configData);
	const rapidjson::Value& languages = config["Languages"];
	for (rapidjson::SizeType i = 0; i < languages.Size(); ++i) {
		Language current;
		if (languages[i]["intLang"].GetBool()) {
			current = Language(languages[i]["name"].GetString(),
					languages[i]["sourceSuffix"].GetString(), "", "",
					languages[i]["exec"].GetString(),
					languages[i]["intLang"].GetBool());
		} else {
			current = Language(languages[i]["name"].GetString(),
					languages[i]["sourceSuffix"].GetString(),
					languages[i]["progSuffix"].GetString(),
					languages[i]["compile"].GetString(),
					languages[i]["exec"].GetString(),
					languages[i]["intLang"].GetBool());
		}
		lang.push_back(current);
	}
	close(configFile);

	int submitFile = open(file.c_str(), O_RDONLY);
	char *submitData = (char*) mmap(NULL, lseek(submitFile, 0, SEEK_END),
			PROT_READ, MAP_PRIVATE, submitFile, 0);
	rapidjson::Document submit;
	submit.Parse(submitData);
	oj = submit["oj"].GetString();
	problem = submit["problem"].GetString();
	submitid = submit["submitid"].GetString();
	language = submit["language"].GetString();
	location = "exec/" + submitid;
	mkdir(location.c_str(), 0777);
	std::string code = submit["code"].GetString();

	std::vector<Language>::iterator current = find(lang.begin(), lang.end(),
			language);
	if (current->sourceSuffix == "") {
		std::ofstream out(location + "/source");
		out << code;
		out.close();
	} else {
		std::ofstream out(location + "/source." + current->sourceSuffix);
		out << code;
		out.close();
	}
	close(submitFile);

	int datalistFile = open(("data/" + problem + "/datalist.json").c_str(),
			O_RDONLY);
	char *datalistData = (char*) mmap(NULL, lseek(datalistFile, 0, SEEK_END),
			PROT_READ, MAP_PRIVATE, datalistFile, 0);
	rapidjson::Document datalist;
	datalist.Parse(datalistData);
	int deTime = 1000, deMemory = 131072, begin = 0, end = -1;
	std::string deComparer, inSuffix = ".in", outSuffix = ".out";
	if (datalist.HasMember("time") && datalist["time"].IsInt())
		deTime = datalist["time"].GetInt();
	if (datalist.HasMember("memory") && datalist["memory"].IsInt())
		deMemory = datalist["memory"].GetInt();
	if (datalist.HasMember("comparer") && datalist["comparer"].IsString())
		deComparer = datalist["comparer"].GetString();
	if (datalist.HasMember("auto") && datalist["auto"].IsBool()
			&& datalist["auto"].GetBool()) {
		if (datalist.HasMember("inSuffix") && datalist["inSuffix"].IsString())
			inSuffix = datalist["inSuffix"].GetString();
		if (datalist.HasMember("outSuffix") && datalist["outSuffix"].IsString())
			outSuffix = datalist["outSuffix"].GetString();
		if (datalist.HasMember("begin") && datalist["begin"].IsInt())
			begin = datalist["begin"].GetInt();
		if (datalist.HasMember("end") && datalist["end"].IsInt())
			end = datalist["end"].GetInt();
		for (int i = begin; i <= end; ++i) {
			Testpoint now;
			now.time = deTime;
			now.memory = deMemory;
			now.comparer = deComparer;
			now.input = "data/" + problem + "/" + udf::conv(i) + inSuffix;
			now.output = "data/" + problem + "/" + udf::conv(i) + outSuffix;
			data.push_back(now);
		}
	} else {
		if (datalist.HasMember("testpoints")) {
			const rapidjson::Value& testpoints = datalist["testpoints"];
			for (rapidjson::SizeType i = 0; i < testpoints.Size(); ++i) {
				Testpoint now;
				if (testpoints[i].HasMember("time")
						&& testpoints[i]["time"].IsInt())
					now.time = testpoints[i]["time"].GetInt();
				else
					now.time = deTime;
				if (testpoints[i].HasMember("memory")
						&& testpoints[i]["memory"].IsInt())
					now.memory = testpoints[i]["memory"].GetInt();
				else
					now.memory = deMemory;
				if (testpoints[i].HasMember("input")
						&& testpoints[i]["input"].IsString())
					now.input = "data/" + problem + "/"
							+ testpoints[i]["input"].GetString();
				else
					continue;
				if (testpoints[i].HasMember("output")
						&& testpoints[i]["output"].IsString())
					now.output = "data/" + problem + "/"
							+ testpoints[i]["output"].GetString();
				else
					continue;
				if (testpoints[i].HasMember("comparer")
						&& testpoints[i]["comparer"].IsString())
					now.comparer = testpoints[i]["comparer"].GetString();
				else
					now.comparer = deComparer;
				data.push_back(now);
			}
		}
	}
	close(datalistFile);
}
void Judger::dump() {
	printf("[Final results]:\n");
	printf("\tStatus: %d\n\n", this->status);
	printf("[Test points]:\n");
	for (std::vector<Testpoint>::iterator it = data.begin(); it != data.end();
			++it) {
		printf("\t-----------------------\n"
				"\tStatus: %d\n"
				"\tTime: %dms\n"
				"\tMemory: %dKB\n"
				"\tExecTime: %dms\n"
				"\tExecMemory: %dKB\n"
				"\tinput: \"%s\"\n"
				"\tOutput: \"%s\"\n"
				"\tComparer: \"%s\"\n"
				"\tInforation: \"%s\"\n", it->status, it->time, it->memory,
				it->execTime, it->execMemory, it->input.c_str(),
				it->output.c_str(), it->comparer.c_str(), it->info.c_str());
	}
}
void Judger::dump2() {

	rapidjson::Document result;
	result.SetObject();
	rapidjson::Value status((int) this->status), totalTime(this->totalTime),
			totalMemory(this->totalMemory), testpoints, compileInfo;
	compileInfo.SetString(this->compileInfo.c_str(), this->compileInfo.length(),
			result.GetAllocator());
	testpoints.SetArray();
	result.AddMember("status", status, result.GetAllocator());
	result.AddMember("time", totalTime, result.GetAllocator());
	result.AddMember("memory", totalMemory, result.GetAllocator());
	result.AddMember("testpoints", testpoints, result.GetAllocator());
	result.AddMember("compileinfo", compileInfo, result.GetAllocator());

	for (std::vector<Testpoint>::iterator it = data.begin(); it != data.end();
			++it) {
		rapidjson::Value now;
		now.SetObject();
		rapidjson::Value time(it->execTime), memory(it->execMemory), info,
				status((int) it->status);
		info.SetString(it->info.c_str(), it->info.length(),
				result.GetAllocator());
		now.AddMember("status", status, result.GetAllocator());
		now.AddMember("time", time, result.GetAllocator());
		now.AddMember("memory", memory, result.GetAllocator());
		now.AddMember("info", info, result.GetAllocator());
		result["testpoints"].PushBack(now, result.GetAllocator());
	}

	rapidjson::StringBuffer buffer;
	rapidjson::Writer < rapidjson::StringBuffer > writer(buffer);
	result.Accept(writer);

	mysql_query(&conn,
			((std::string) "update submit set status='"
					+ udf::conv((int) this->status) + "' where submitid='"
					+ submitid + "'").c_str());
	mysql_query(&conn,
			((std::string) "update submit set info='" + buffer.GetString()
					+ "' where submitid='" + submitid + "'").c_str());
	mysql_query(&conn,
			((std::string) "update submit set time='"
					+ udf::conv(this->totalTime) + "' where submitid='"
					+ submitid + "'").c_str());
	mysql_query(&conn,
			((std::string) "update submit set memory='"
					+ udf::conv(this->totalMemory) + "' where submitid='"
					+ submitid + "'").c_str());
}
void Judger::start() {
	if (data.size() == 0) {
		status = UKE;
		return;
	}

	Compiler compiler(location);
	compiler.start(language);
	compileInfo = compiler.information;
	Base64 *base = new Base64();
	compileInfo = base->Encode(compileInfo.c_str(), compileInfo.length());
	if (!compiler.success) {
		status = CE;
		return;
	}

	int execId = 0;
	bool notAC = false;
	for (std::vector<Testpoint>::iterator it = data.begin(); it != data.end();
			++it) {
		Monitor now(++execId, it->time, it->memory, it->input, location);
		now.start();
		it->status = now.status;
		it->execTime = now.execTime;
		it->execMemory = now.execMemory;
		if (it->status != AC)
			continue;
		int compPid = 0;
		if ((compPid = fork()) == 0) {
			setpgid(0, 0);
			execlp("/bin/bash", "/bin/bash", "-c",
					("./comparer " + location + "/" + udf::conv(execId) + ".out"
							+ " " + it->output + " " + location).c_str(), NULL);
			exit(0);
		} else {
			int retCode;
			pid_t now = 1;
			do {
				now = waitpid(compPid, &retCode, WNOHANG);
			} while (now == 0);
			if (!retCode) {
				it->status = AC;
			} else {
				it->status = WA;
				std::ifstream compInfo(location + "/compinfo");
				std::string buf;
				while (getline(compInfo, buf))
					it->info += buf;
			}
		}
		dump2();
	}
	for (std::vector<Testpoint>::iterator it = data.begin(); it != data.end();
			++it) {
		if (it->status != AC) {
			notAC = true;
			this->status = it->status;
			break;
		}
	}
	if (!notAC)
		this->status = AC;

	for (std::vector<Testpoint>::iterator it = data.begin(); it != data.end();
			++it) {
		totalTime += it->execTime;
		totalMemory += it->execMemory;
	}
}
void checkMemory(std::string execPid, int &memPeak) {
	static std::string _I;
	std::ifstream ifs("/proc/" + execPid + "/status");
	ifs >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I
			>> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I
			>> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I
			>> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I >> _I
			>> _I >> _I >> memPeak;
}
}
#endif
