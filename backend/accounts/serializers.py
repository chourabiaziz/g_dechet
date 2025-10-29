from rest_framework import serializers
from .models import LegacyUser

class RegisterSerializer(serializers.ModelSerializer):
    plain_password = serializers.CharField(write_only=True, min_length=6)

    class Meta:
        model = LegacyUser
        fields = ['email', 'name', 'prename', 'countryCode', 'phone', 'plain_password']

    def create(self, validated_data):
        password = validated_data.pop('plain_password')
        user = LegacyUser(**validated_data)
        user.set_password(password)
        # If table uses different column names or requires other fields, adapt here
        user.save()
        return user
