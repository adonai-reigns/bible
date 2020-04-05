#!/bin/bash

#VERSION="main-int"
VERSION="main-leb"

echo "Running latexmk -xelatex -quiet -f $VERSION.tex"

latexmk -xelatex -quiet -f "$VERSION.tex"

rm "$VERSION.fdb_latexmk"
rm "$VERSION.fls"
rm "$VERSION.out"
rm "$VERSION.toc"
rm "$VERSION.log"


