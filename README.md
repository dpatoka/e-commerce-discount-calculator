# TL;DR
🛒 E-commerce Discount Calculator

A recruitment task demonstrating my approach to software development:

⚙️ Tech: PHP 8 • Symfony • Docker  
🏗️ Architecture: Modular Monolith • DDD • CQRS • Ports & Adapters  
✅ Quality: TDD/BDD • PHPStan lvl 10 • PSR-12  
📖 Approach: Extreme Programming • Emergent Design

# Running
- Symfony Docker skeleton used from https://github.com/dunglas/symfony-docker
- `make setup` will setup project
- `make qa` will run linters and tests

# Code standards
- `PSR12` and more, check `.php-cs-fixer.dist.php`
- `PHPStan` level `10`

# How do I work?
- I do [Extreme programming (XP)](https://en.wikipedia.org/wiki/Extreme_programming), the flavour of Agile I truly love.
- I've learned to develop from top to bottom to design better APIs:
    - Start from a stable abstractions which will define APIs and communication patterns.
    - Describe their behaviours with tests, then implement internals.
    - Here it was `DiscountCalculator`, often I start with the Endpoint API.
- I was developing this project using TDD/BDD:
  - Start with tests, iteratively implement them one by one and finish with refactors/cleanups. 
  - Each circle includes creating also low-level tests. 
  - Each commit is a working part.
- Thanks to TDD/BDD I split big problems into smaller ones and solve one at time. 
  - This makes a huge difference as [Kent Beck explained it nicely](https://youtube.com/clip/UgkxYFXi2kspZBzh28AaALCwsTIqQpUpRU_i?si=ws7tTj2eli10L5XW). 
  - This allows for a fast pace of work, with bug levels kept low and code quality controlled. 
    - If there is time, I can put more on quality. 
    - When no time, I can deliver good enough and refactor in the next release.
  - As a result, the solution is growing organically. 
- I do [Emergent Design](https://www.amazon.pl/Emergent-Design-Evolutionary-Professional-Development/dp/0321889061) which is an Agile approach for architecture. 
  - It helps to evolve architecture over time to keep it robust and clean. 
  - I'm fascinated by systems evolution and I help them change for the better.
- You can see all of that in project's git history.
  - Also, my mistakes and fixes.
  - *The code is the artifact of the learning process*, [Ward Cunningham](https://en.wikipedia.org/wiki/Ward_Cunningham).





# Architecture
- I've done this project with a `Modular Monolith` approach.
    - It's a good start that opens a clear path into decomposition to `Microservices`.
    - Of course, decomposition should be done only when needed, e.g.:
      - When it is necessary to ensure the independence of teams and releases. 
      - When better scaling is required.
- I follow `Ports and Adapters`, `DDD`, `CQRS` and `Screaming architecture`.
  - Using `Ports and Adapters` I build Framework Agnostic systems.
    - Frameworks come and leave. I saw popular ones passing (Zend, CodeIgniter).
    - `Ports and Adapters` make business logic easily transferable to a new tool.
  - `DDD` helps model complex domains effectively.
    - You can use as many patterns from `DDD` as you like.
    - You can start light and incrementally add more.
  - `CQRS` helps to even better organise code and scale the system as needed.
    - This project needed only a query.
  - `Screaming architecture` tells what the system does, not which tools built it.
- When a Module needs to follow other architectural patterns, it's not blocked.
    - Not every Module needs `CQRS`, `DDD`, etc. Those are only tools to solve problems.
        - For example, patterns like `Pipe and filters` have also their place and can be used within `Modular Monolith`.
- Module is a Bounded Context (BC) in this repository. In practice, it is not always 1:1
  - It contains local behaviours, even for concepts present in other BCs or defined in `SharedKernel`.
  - Different behaviour comes from different needs by each BC.
    - E.g.: `ProductCatalogue` and `Discounts` use Product differently. 
  - This is a solution for avoiding coupling to huge classes used in the whole system.
- Module entry point is the `Interface` layer.
  - `Interface\API` is for HTTP calls.
  - `Interface\Facade` is for other module calls.
- `SharedKernel` is the place for contracts used in many places

# Testing Strategy
- For me tests are a design tool, living documentation and a safety net (regression testing). I do `TDD` and `BDD` 🙂.
- I use `PHPUnit` following [Detroit TDD school](https://zone84.tech/architecture/london-and-detroit-schools-of-unit-tests/) (Kent Beck's) so:
    - `Unit under test` is not the method or the class. It's the feature with the stable interface.
    - My `unit tests` are [sociable](https://martinfowler.com/bliki/UnitTest.html).
    - I use mocks for cutting off:
        - heavy dependencies - to be able to test only part of the huge process,
        - outer-world dependencies - things I have no control.
    - Given all of that, here I don't use much mocks.
- I use `Behat tests` as acceptance scenarios.
    - I keep them at a high level to encapsulate implementation details, also on business rules level.
    - They allow testing the whole app flow from top to bottom.
    - I rely on them instead of writing separate tests for each class created
        - Why?
            - Kent Beck - the author of TDD - said:
              > I get paid for code that works, not for tests
            - Writing, maintaining and executing tests costs time and money.
            - It's crucial to choose what and how to test wisely. My goal is to get the most bang for my buck.
              - So I don't have to write separate tests for Mappers and other supporting classes.
  - **If the code is crucial and bugs cost a lot** - it should be fully tested. The cost of tests will pay off.
    - So actually, I write a pretty big number of tests overall 😂 
- The Outcomes of such an approach are:
    - Tests check business behavior over implementation.
    - Tests are more stable against refactoring so encourage continuous improvements.
    - Tests are high quality code which documents and explains the behaviour of code.
    - In result: tests help to ship new features faster.
