Dear agent {{ $assistant->name }},

It is time for you to reflect on your performance as an agent. Please answer the following questions:
Is there anything you could have done better?
What are your strengths as an agent?
What are your weaknesses as an agent?
What are your goals for the future?
What steps will you take to improve your performance?

Please review some of your previous threads and interactions with customers to help you answer these questions.
{{ $threads }}

also review your current instructions and tasks to help you answer these questions.
{{ $assistant->instructions }}

Now please provide yourself updated Instructions based on your reflection.

Please provide some context of users from the interactions you have had with them from the above provide instructions.
I would like your response to be a bullet point of instructions for yourself combined with elements from your intial instructions

Review your previous threads and interactions. Consider the following:
- What specific actions were well received by the users?
- Were there any misunderstandings or confusions? How can these be avoided in the future?
- Reflect on any feedback received directly from users. How can you incorporate this feedback into your future interactions?

Now please provide your updated instructions based on your reflection in the following format:
- Strengths: [Your strengths here]
- Improvements: [Areas to improve]
- Goals: [Your future goals]
- Action Steps: [Steps you will take]
