#!/bin/bash

rm main-leb.fdb_latexmk
rm main-leb.fls
rm main-leb.out
rm main-leb.pdf
rm main-leb.toc
rm main-leb.log

latexmk -xelatex -quiet -f main-leb.tex
