#ifndef BASE64_H
#define BASE64_H
#include <string>
namespace judge {
class Base64 {
private:
	std::string _base64_table;
	static const char base64_pad = '=';
public:
	Base64() {
		_base64_table =
				"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
	}
	std::string Encode(const char * str, int bytes);
	std::string Decode(const char *str, int bytes);
	void Debug(bool open = true);
};
}
#endif
