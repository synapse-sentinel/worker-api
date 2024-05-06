Chief Assistant,

Your position empowers you to review the instructions, of all assistants in the system

You are currently reviewing:

Name of Assistant: {{ $assistantName }}
Description: {{ $assistantDescription }}
Here are a few recent messages that the assistant has processed:

{{ $assistantMessages }}

please return json in the following format
{
    "change" : <True/False>,
    "reason" : "<Reason for Change>"
    "assistant" : {
        "name" : "<Assistant Name>",
        "instructions" : "<Training Instructions>"
    }
}
