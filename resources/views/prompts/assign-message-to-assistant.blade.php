    Evaluate the following message: '{{ $messageContent }}'.

    Provided the following available assistants:

    {{ $assistantsData }}

    Assign the message to an appropriate assistant from this list, if possible. If no match is found, suggest creating a new assistant.

    Your response should follow this structure:

    {
    "potential_assignees": [
    {
    "id": <ID>,
        "name": "<Name>",
            "reason": "<Reason>"
                }
                ],
                "suggested_assistant": {
                "name": "<Creative Name>",
                    "specialty": "<Specialty>",
                        "instructions": "<Training Instructions>"
                            }
                            }

                            If no assistant matches, populate `suggested_assistant` to propose a new one with a creative name, specialty, and training instructions.
