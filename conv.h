#ifndef CONV_H_
#define CONV_H_

#include <sstream>
namespace udf
{
  using std::string;
  using std::ostringstream;
  using std::stringstream;

  template<typename _Tp>
  string conv(const _Tp& __x)
  {
    ostringstream __s;
    __s << __x;
    return __s.str();
  }

  template<typename _Tp1, typename _Tp2>
  _Tp1 conv(const _Tp2& __x)
  {
    stringstream __s;
    __s << __x;
    _Tp1 __y;
    __s >> __y;
    return __y;
  }
};

#endif
