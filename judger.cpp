#include "judgedef.h"

using namespace judge;

int main(int argc, char** argv) {
	if (argc != 2) {
		printf("%s", ((std::string)"Usage: "+argv[1]+" Submission_File").c_str());
	}
	Judger a(argv[1]);
	a.start();
	a.dump2();
	return 0;
}
