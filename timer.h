#ifndef TIMER_H_
#define TIMER_H_
#include <sys/time.h>

namespace xszq
{

class Timer
{
public:
	struct timeval	begin;
	struct timeval	end;
	void			start();
	void			stop();
	double			sec();
	double			msec();
	double			usec();
	Timer();
};

Timer::Timer() {
}
void Timer::start() {
	gettimeofday(&begin, 0);
	end = begin;
}
void Timer::stop() {
	gettimeofday(&end, 0);
}
double Timer::sec() {
	return ((end.tv_sec-begin.tv_sec)*1000000.0+(end.tv_usec-begin.tv_usec))/1000000.0;
}
double Timer::msec() {
	return ((end.tv_sec-begin.tv_sec)*1000000.0+(end.tv_usec-begin.tv_usec))/1000.0;
}
double Timer::usec() {
	return (end.tv_sec-begin.tv_sec)*1000000.0+(end.tv_usec-begin.tv_usec);
}

}

#endif
