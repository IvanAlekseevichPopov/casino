package main

import (
	"flag"
	"fmt"
	"log"
	"os"
	"strconv"
	"time"
)

func main() {
	startTime := time.Now()
	var i, totalCounter int64
	var fileName string
	var goroutinesCount int64 = 30
	ch := make(chan int64)

	var chipCount, fieldCount int

	flag.IntVar(&chipCount, "c", 18, "Количество фишек")
	flag.IntVar(&fieldCount, "f", 36, "Количество ячеек")
	flag.StringVar(&fileName, "F", "result.txt", "Файл для сохранения")
	flag.Parse()
	if chipCount > fieldCount {
		fmt.Println("Количество фишек больше количества полей")
		os.Exit(1)
	}

	startInt, finishInt := decimalShapesofBinary(chipCount, fieldCount)

	rangeOfInt := ((finishInt - startInt) / goroutinesCount)
	// fmt.Println(rangeOfInt)
	// fmt.Println(startInt)

	for i = 0; i < int64(goroutinesCount); i++ {
		start := int64(startInt) + int64(rangeOfInt)*i
		finish := start + int64(rangeOfInt) - 1
		if i == int64(goroutinesCount)-1 {
			finish = int64(finishInt)
		}
		go thread(
			start,
			finish,
			chipCount,
			ch,
		)
	}

	file, err := os.Create(fileName)
	if err != nil {
		os.Exit(1)
	}
	defer file.Close()
	for i = 0; i < int64(goroutinesCount); i++ {
		x := <-ch
		fmt.Println(x)
		totalCounter = totalCounter + x
		file.WriteString(string(totalCounter))
	}

	fmt.Printf("\nИтого %v\n", totalCounter)
	fmt.Println("Время выполнения  ", time.Since(startTime))
}

func ones(n int64) int {
	w := 0
	for n != 0 {
		w++
		n &= n - 1
	}
	return w
}

func thread(start int64, finish int64, chipCount int, ch chan int64) {
	var c, i int64
	for i = start; i <= finish; i++ {
		if ones(i) == chipCount {
			c++
		}
	}
	ch <- c
}

func decimalShapesofBinary(chipCount int, fieldCount int) (int64, int64) {
	var start, finish string
	for i := 0; i < fieldCount; i++ {
		if i < chipCount {
			finish += "1"
		} else {
			finish += "0"
		}
		if i < fieldCount-chipCount {
			start += "0"
		} else {
			start += "1"
		}
	}
	startInt, err := strconv.ParseInt(start, 2, 64)
	if nil != err {
		log.Fatal(err)
	}
	finishInt, err := strconv.ParseInt(finish, 2, 64)
	if nil != err {
		log.Fatal(err)
	}

	return startInt, finishInt
}
