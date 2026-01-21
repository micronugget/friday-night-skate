# Guidance: Assembling an AI Agent Team on Copilot

Assembling a team of specialized agents using GitHub Copilot (and "Mission Control" concepts) is a powerful way to scale your development and maintenance capabilities. Based on the provided resources and industry best practices, here is guidance on how to orchestrate this team effectively.

### 1. Implementation Strategy: "Mission Control" Pattern
Following the GitHub blog post's philosophy, you should treat your agents as discrete entities with clear boundaries.

- **Centralized Context:** Store your agent definitions (the `.md` files created in the `agents/` directory) in your repository. This ensures all team members (human and AI) have a shared understanding of roles.
- **Project Rules:** Use `.github/copilot-instructions.md` (or similar project-level settings) to point Copilot towards these agent definitions. You can instruct the primary Copilot interface to "Assume the role of [Agent Name] defined in agents/[agent].md" for specific tasks.
- **Task Hand-offs:** Explicitly define how work moves between agents. 
    - *Example:* Developer agent finishes a feature -> Technical Writer agent reviews the diff and updates the README -> Tester agent runs the validation suite.

### 2. What Might Be Missing in Your Planning
While you have the core roles (Dev, Writer, Tester), a high-functioning team often needs these additional "glue" components:

- **The Architect/Coordinator (Mission Control):** A role responsible for breaking down high-level goals into tasks for the specialized agents. This is often the human user, but can be assisted by an "Orchestrator" agent that manages the roadmap.
- **Environment Management:** You need a way for the Tester agent to actually *execute* code safely. Infrastructure-as-Code (like your Ansible project) is great, but you need a "sandbox" (CI/CD pipelines, ephemeral environments) where the agent can observe the results of its actions.
- **Feedback Loops:** How do agents learn from each other's failures? If the Tester finds a recurring bug type, there should be a mechanism to update the Developer agent's "Guiding Principles" to prevent it in the future.
- **Version Control for Agents:** As your project evolves, your agents' instructions will need to evolve too. Treat your `agents/*.md` files as code: version them, peer-review changes to them, and ensure they stay aligned with the project's complexity.

### 3. Practical Steps for Copilot Integration
1.  **Instruction Files:** Keep your agent files concise but highly specific. LLMs perform better with "Positive Constraints" (Tell them what *to* do and *how* to do it, rather than just what *not* to do).
2.  **Use `@workspace` effectively:** When interacting with Copilot, use the `@workspace` command along with a reference to your agent file. *e.g., "@workspace as per agents/tester.md, what are the potential regressions in this PR?"*
3.  **Iterative Refinement:** Start with simple instructions. As you notice an agent making specific types of mistakes, update the corresponding `.md` file with a "Constraint" or "Guiding Principle" to address that exact issue.

### 4. Summary of the Team
| Agent | Primary Output | Success Metric |
| :--- | :--- | :--- |
| **Architect** | Task Lists & Architecture Docs | Project Cohesion & Alignment |
| **Ansible Developer** | Executable Ansible Code | Idempotency & Functionality |
| **Drupal Developer** | Drupal Modules & Configuration | Code Quality & Performance |
| **UX/UI Designer** | Design Systems & Frontend Code | Visual Quality & Usability |
| **Environment Manager** | Provisioned Sandbox Envs | Parity & Reproducibility |
| **Technical Writer** | Markdown Documentation | User Clarity & Doc Accuracy |
| **Tester** | Test Reports & Bug Logs | Regression Rate & Code Coverage |
