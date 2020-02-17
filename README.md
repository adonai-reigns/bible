The Lexham English Bible
====================

![In the beginning God created the heavens and the earth](preview.png?raw=true)

I am using LaTeX to typeset the Lexham English Bible.

This project has been forked from an existing project where the King James Version of the Holy Bible was being typeset.

The book has been prepared for print at www.snowfallpress.com as a 6 inch by 9 inch perfect-bound paperback, with 40lb thin paperstock. The resulting file produces 1008 pages and a spine being 1.58 inches thick.

The text for this translation has been converted to LaTeX format from XML source code freely available at http://www.logos.com.

The proprietary fonts used in the original fork have been replaced by free Open-Source fonts.

Building
--------

You will need XeLaTeX to compile this. 

```bash
$ latexmk -xelatex main-leb.tex
```

Q & A
-----

**Why are you doing this?**

I have been looking for a reliable translation of the Holy Bible as my knowledge of the original message grows to show errors in the mainstream translations. At a quick review of the Lexham English Translation, I decided it is worth investigating the text more thoroughly. I intend to carry this version as my daily bible and mark notes to indicate any changes that I find should be necessary.


**Why the *Lexham English* Bible?**

1. It has a license that is designed to support and sustain the freedom to use the translation, and to protect it from competing interests. Other translations have precisely the opposite spirit.

2. Upon initial checks, it seems to belong to a very rare and precious set of translations that have escaped the corrupting influences of certain doctrinal heresies that have inspired wild distortions to the message of the texts in the majority of other translations.

3. The LEB bible is presently not accessible in a printed format, and the license terms specifically have discouraged any commercial interests from producing it. This project makes the translation more accessible for readers who might like to print their own copy but would be unable to convert the XML to a print-ready PDF file.
