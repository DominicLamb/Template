**Note**: This project serves as an example of my approach to software development circa 2015. It is provided without warranty or license, 
with an intent of demonstrating contemporary abilities regarding project structure and thoughts regarding testing.

# Template
This project implements a simple templating library within PHP, essentially serving as a namespaced token replacement engine. Namespaces
may be loaded at runtime, and each namespace may introduce its own interpreting logic to e.g. automatically escape its values.

Five years is a long time, and in that time I've considered this problem enough to believe that although the approach is viable, it no
longer best represents the way in which I would personally structure this project. For a start, concepts are badly named, e.g. the
`RenderTree` is not actually a tree, but a flat map with the potential to be recursively referenced elsewhere. The addition of
custom namespace interpretation is an interesting short-term convenience, and solves the goals this project is trying to implement,
but it's likely that the behaviour is by no means easy to maintain.

With those criticisms said, it appears that the scenario coverage is reasonable, with edge cases explicitly identified. My overall
approach to testing has matured since this was implemented, but has not diverged enough for this to be unrecognisable to me.

Fundamentally, however, this library serves very little purpose in most avenues of modern web development. With the move toward
single-page applications and separation of concerns between views and service layers, there is a significantly-reduced need for
server-side templating of this nature. The state of web development in 2020 promotes the use of background web requests to APIs
for anything serving dynamic data. There is limited scope for this to be useful within a static site generator, however, or for
the purpose of crafting e-mails.

The library is viewable for historical reasons and no support is available. Feel free to browse, but please do not use this
unlicensed, untested library for anything important.
